<?php

namespace App\Repositories;

use App\Models\UserRole;

class UserRoleRepository implements UserRoleRepositoryInterface
{
    public function getAllUserRoles()
    {
        return UserRole::all();
    }
}