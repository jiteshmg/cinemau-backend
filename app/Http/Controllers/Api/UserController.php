<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepositoryInterface;

class UserController extends Controller
{
    use JsonResponseTrait;
    protected $userRepository;
    
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => "required|string",
                'email' => "required|string|unique:users",
                'password' => "required|min:4|confirmed"
            ]
        );
        
        if ($validator->fails()) {
            return $this->errorResponse("Validation error occurred", 400, $validator->errors());
        }

        $user = $this->userRepository->createUser([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return $user->id 
            ? $this->successResponse($user, "User created")
            : $this->errorResponse("Something went wrong", 404);
    }

    public function getUsers(): JsonResponse
    {
        try {
            $users = $this->userRepository->getAllUsers();
            return $this->successResponse($users, count($users) . " user(s) fetched");
        } catch (Exception $e) {
            return $this->errorResponse("API failed due to an error", 500, $e->getMessage());
        }
    }

    public function getUserDetail(int $id): JsonResponse
    {
        $user = $this->userRepository->getUserById($id);
        return $user 
            ? $this->successResponse($user, "User found")
            : $this->errorResponse("User not found", 404);
    }

    public function updateUser(Request $request, $id): JsonResponse
    {
        Log::info('Raw request content:', [$request->getContent()]);
        Log::info('All Request Data:', $request->all());

        $user = $this->userRepository->findUserById($id);
        if (!$user) {
            return $this->errorResponse("User not found", 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'nullable|string',
            'mobile' => 'sometimes|numeric|digits:10|nullable',
            'image_path' => 'nullable|string',
            'roles' => 'sometimes|array',
            'roles.*' => 'integer|exists:roles,id'
        ]);

        $roles = $validated['roles'] ?? [];
        unset($validated['roles']);

        $updatedUser = $this->userRepository->updateUser($user, $validated);
        $updatedUser = $this->userRepository->updateUserRoles($user, $roles);

        return $this->successResponse($updatedUser, 'User updated successfully');
    }

    public function updateImage(Request $request, $id): JsonResponse
    {
        Log::info('Image Upload Request:', [$request->all()]);

        $user = $this->userRepository->findUserById($id);
        if (!$user) {
            return $this->errorResponse("User not found", 404);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $path = $request->file('image')->store('public/profile_images');
        $imageUrl = Storage::url($path);

        $updatedUser = $this->userRepository->updateUserImage($user, $imageUrl);

        return $this->successResponse($updatedUser, 'Image uploaded successfully');
    }

    public function deleteUser($id): JsonResponse
    {
        $user = $this->userRepository->findUserById($id);
        if (!$user) {
            return $this->errorResponse("User not found", 404);
        }
        
        $this->userRepository->deleteUser($user);
        return $this->successResponse(null, "User has been deleted successfully");
    }
   
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation errors', 400, $validator->errors());
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);
            $data = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'token' => $token
            ];
            return $this->successResponse($data, 'Login successful');
        }
        
        return $this->errorResponse('Unauthorized. Invalid email or password.', 401);
    }

    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();
        return $this->successResponse(null, 'Successfully logged out');
    }

    public function getUserByRoleId($roleId): JsonResponse
    {
        $users = $this->userRepository->getUsersByRoleId($roleId);
        return $this->successResponse($users, 'Users retrieved successfully');
    }

    public function getUserRolesByUserId(int $userId): JsonResponse
    {
        $roles = $this->userRepository->getUserRoles($userId);
        return $roles 
            ? $this->successResponse($roles, 'Roles retrieved successfully')
            : $this->errorResponse('User not found', 404);
    }
}