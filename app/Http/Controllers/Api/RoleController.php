<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;

use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;
use App\Repositories\RoleRepositoryInterface;

class RoleController extends Controller
{
    use JsonResponseTrait;
    
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Summary of getRoles
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoles(): JsonResponse
    {
        try {
            $roles = $this->roleRepository->getAllRoles();
            return $this->successResponse($roles, 'Roles retrieved successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}