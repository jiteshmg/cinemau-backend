<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Role;
use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    use JsonResponseTrait;
    /**
     * Summary of getRoles
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoles():JsonResponse {
        $roles = Role::all();
        try{
            return $this->successResponse($roles, 'Roles retrieved successfully');
        }catch(Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}