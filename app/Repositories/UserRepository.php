<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserRepository implements UserRepositoryInterface
{
    public function updateUser($user, array $data)
    {
        $user->update($data);
        return $user;
    }
    public function updateUserRoles($user, array $roles)
    {
        $user->roles()->sync($roles);
        return $user->load('roles');
    }

    public function updateUserImage($user, $imagePath)
    {
        // Delete old image if exists
        if ($user->image_path) {
            Storage::delete(str_replace('storage/', 'public/', $user->image_path));
        }

        // Update the user's image path
        $user->update(['image_path' => $imagePath]);
        return $user;
    }
}