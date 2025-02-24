<?php

namespace App\Repositories;

interface MovieInvitationRepositoryInterface
{
    public function createInvitations(array $invitations);
    public function getPendingInvitations(int $userId);
    public function findInvitationByIdAndUser(int $id, int $userId);
    public function updateInvitationStatus($invitation, string $status);
    public function createMovieCrew(array $data);
}