<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserRoleController;


Route::post('login',[UserController::class,'login']);
Route::post('register',[UserController::class,'createUser']);
Route::put('user-update/{id}', [UserController::class, 'updateUser']);
Route::post('update-profile-image/{id}', [UserController::class, 'updateImage']);

// Route::put('user-update/{id}', function ($id) {
//     return response()->json(['message' => 'Route hit', 'id' => $id]);
// });


// Route::delete('delete-user/{id}',[UserController::class,'deleteUser']);



// Route::get('unauthenticate',[UserController::class,'unauthenticate'])->name('unauthenticate');

// Route::get('get-user',[UserController::class,'getUsers']);
Route::get('get-user/{id}',[UserController::class,'getUserDetail']);
// Route::post('logout',[UserController::class,'logout']);

Route::get('role',[RoleController::class,'getRoles']);
Route::get('user-role',[UserRoleController::class,'getUserRoles']);
