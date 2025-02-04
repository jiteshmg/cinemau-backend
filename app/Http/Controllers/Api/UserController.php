<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;



class UserController extends Controller
{
    /**
     * Summary of createUser
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function createUser(Request $request): mixed
    {

        $validator = Validator::make(
            $request->all(),
            [
                'name' => "required | string",
                'email' => "required | string| unique:users",
                'password' => "required|min:4|confirmed"
            ]
        );
        if ($validator->fails()) {
            $result = array(
                'status' => false,
                'message' => "Validation error occured",
                'error_message' => $validator->errors()
            );
            return response()->json($result, 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($user->id) {
            $result = array('status' => true, 'message' => "User created", "data" => $user);
            $responseCode = 200;
        } else {
            $result = array('status' => false, 'message' => "Something went wrong");
            $responseCode = 404;
        }
        return response()->json($result, $responseCode);

    }

    /**
     * Summary of getUsers
     * @return mixed
     */
    public function getUsers()
    {
        $users = User::all();
        try {
            $result = array('status' => true, 'message' => count($users) . " user(s) fetched", "data" => $users);
            $responseCode = 200; // Success
            return response()->json($result, $responseCode);
        } catch (Exception $e) {
            $result = array(
                'status' => false,
                'message' => "API failed due to an error",
                "error" => $e->getMessage()
            );
            return response()->json($result, 500);
        }

    }

    /**
     * Summary of getUserDetail
     * @param integer $id
     * @return mixed
     */
    public function getUserDetail(int $id): JsonResponse {
        $user = User::with('roles')->find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => "User not found"], 404);
        }
        $result = array('status' => true, 'message' => "User found", "data" => $user);
        return response()->json($result, 200);
    }

    /**
     * Summary of updateUser
     * @param \Illuminate\Http\Request $request
     * @param integer $id
     * @return mixed
     */
    public function updateUser(Request $request, $id): JsonResponse
    {
        Log::info('Raw request content:', [$request->getContent()]);
        Log::info('All Request Data:', $request->all()); // Debugging

        // Find user
        $user = User::findOrFail($id);

        // Validate the request
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'nullable|string',
            'mobile' => 'sometimes|numeric|digits:10|nullable',
            'image_path' => 'nullable|string', // Now expecting image path as a string
            'roles' => 'sometimes|array',
            'roles.*' => 'integer|exists:roles,id'
        ]);

        // Extract roles from the validated data
        $roles = $validated['roles'] ?? [];
        unset($validated['roles']);

        // Update user
        $updated = $user->update($validated);
        if (!$updated) {
            return response()->json(['message' => 'Update failed'], 400);
        }

        // Update roles if provided
        $user->roles()->sync($roles);

        return response()->json([
            'data' => $user->load('roles'),
            'message' => 'User updated successfully'
        ]);
    }

    public function updateImage(Request $request, $id): JsonResponse {
        Log::info('Image Upload Request:', [$request->all()]);

        $user = User::findOrFail($id);

        // Validate the image upload
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Delete old image if exists
        if ($user->image_path) {
            Storage::delete(str_replace('storage/', 'public/', $user->image_path));
        }

        // Store new image
        $path = $request->file('image')->store('public/profile_images');
        $imageUrl = Storage::url($path);

        // Update the user's image path
        $user->update(['image_path' => $imageUrl]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image_path' => $imageUrl
        ]);
    }

    #function to delete user
    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => "User not found"], 404);
        }
        $user->delete();
        $result = array('status' => true, 'message' => "User has been deleted successfully");
        return response()->json($result, 200);
    }
    public function login(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 400);
        }

        // Check if the user credentials are valid
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // If you are using session-based authentication, the session will automatically be set
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid email or password.'
            ], 401);
        }
    }

    public function unauthenticate()
    {
        return response()->json(['status' => false, 'message' => "Only authorised user can access", "error" => "unauthenticate"], 401);
    }
    #function to logout user
    public function logout()
    {
        $user = Auth::user();
        /*$user->tokens->each(function ($token, $key) {
        $token->delete();
        });*/
        return response()->json(['status' => true, 'message' => 'Logged out successfully', 'data' => $user], 200);
    }
}

