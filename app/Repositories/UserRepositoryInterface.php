<?php

namespace App\Repositories;

use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function updateUser($user, array $data);
    
    public function updateUserRoles($user, array $roles);
    public function updateUserImage($user, $imagePath);
}