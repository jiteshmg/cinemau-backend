<?php

namespace App\Repositories;

use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function createUser(array $data);
    public function getAllUsers();
    public function getUserById(int $id);
    public function updateUser($user, array $data);
    public function updateUserRoles($user, array $roles);
    public function updateUserImage($user, $imagePath);
    public function deleteUser($user);
    public function findUserById(int $id);
    public function getUsersByRoleId($roleId);
    public function getUserRoles(int $userId);
}