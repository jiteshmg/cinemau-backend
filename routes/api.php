<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\UserRoleController;
use App\Http\Controllers\Api\MovieInvitationController;


Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'createUser']);



// Protected Routes (Require JWT Token)
Route::middleware(['auth:api'])->group(function () {
    Route::put('user-update/{id}', [UserController::class, 'updateUser']);
    Route::get('get-user/{id}', [UserController::class, 'getUserDetail']);
    Route::post('update-profile-image/{id}', [UserController::class, 'updateImage']);
    Route::get('user-role', [UserRoleController::class, 'getUserRoles']);
    Route::get('user-role/{id}', [UserController::class, 'getUserByRoleId']);

    Route::get('role', [RoleController::class, 'getRoles']);
    
    Route::get('movies', [MovieController::class, 'getMovies']);
    Route::get('movie-user/{id}', [MovieController::class, 'getMoviesByUserId']);
    Route::post('join-movie', [MovieController::class, 'joinMovie']);

    Route::post('invite-movie', [MovieInvitationController::class, 'sendInvitation']);
});




