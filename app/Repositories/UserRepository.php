<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserRepository implements UserRepositoryInterface
{
    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function getAllUsers()
    {
        return User::all();
    }

    public function getUserById(int $id)
    {
        return User::with('roles')->find($id);
    }

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
        if ($user->image_path) {
            Storage::delete(str_replace('storage/', 'public/', $user->image_path));
        }
        $user->update(['image_path' => $imagePath]);
        return $user;
    }

    public function deleteUser($user)
    {
        return $user->delete();
    }

    public function findUserById(int $id)
    {
        return User::find($id);
    }

    public function getUsersByRoleId($roleId)
    {
        return User::whereHas('roles', function ($query) use ($roleId) {
            $query->where('roles.id', $roleId);
        })->get();
    }

    public function getUserRoles(int $userId)
    {
        $user = User::with('roles:id,name')->find($userId);
        return $user ? $user->roles : null;
    }
}