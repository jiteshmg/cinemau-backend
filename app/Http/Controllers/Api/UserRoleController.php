<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class UserRoleController extends Controller
{
    public function getUserRoles():JsonResponse {
        $userRoles = UserRole::all();
        try{
            $result = array('status' => true, 'message' => count($userRoles). " user role(s) fetched", "data" => $userRoles);
            return response()->json($result, 200);
        }catch(Exception $e) {
            $result = array('status' => false, 'message' => "API failed due to an error",
            "error" => $e->getMessage());
            return response()->json($result, 500);
        }
    }
}