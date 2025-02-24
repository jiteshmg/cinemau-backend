<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;
use App\Repositories\UserRoleRepositoryInterface;

class UserRoleController extends Controller
{
    use JsonResponseTrait;
    
    protected $userRoleRepository;

    public function __construct(UserRoleRepositoryInterface $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }

    public function getUserRoles(): JsonResponse
    {
        try {
            $userRoles = $this->userRoleRepository->getAllUserRoles();
            return $this->successResponse($userRoles, count($userRoles) . " user role(s) fetched");
        } catch (Exception $e) {
            return $this->errorResponse("API failed due to an error", 500, $e->getMessage());
        }
    }
}