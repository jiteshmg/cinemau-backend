<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * Summary of getRoles
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoles():JsonResponse {
        $roles = Role::all();
        try{
            $result = array('status' => true, 'message' => count($roles). " role(s) fetched", "data" => $roles);
            return response()->json($result, 200);
        }catch(Exception $e) {
            $result = array('status' => false, 'message' => "API failed due to an error",
            "error" => $e->getMessage());
            return response()->json($result, 500);
        }
    }
}