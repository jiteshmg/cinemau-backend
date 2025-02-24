<?php

namespace App\Repositories;

use App\Models\MovieCrew;
use App\Models\MovieInvitation;

class MovieInvitationRepository implements MovieInvitationRepositoryInterface
{
    public function createInvitations(array $invitations)
    {
        return MovieInvitation::insert($invitations);
    }

    public function getPendingInvitations(int $userId)
    {
        return MovieInvitation::where('user_id', $userId)
            ->where('status', 'pending')
            ->with('project', 'role')
            ->get();
    }

    public function findInvitationByIdAndUser(int $id, int $userId)
    {
        return MovieInvitation::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function updateInvitationStatus($invitation, string $status)
    {
        $invitation->update(['status' => $status]);
        return $invitation;
    }

    public function createMovieCrew(array $data)
    {
        return MovieCrew::create($data);
    }
}