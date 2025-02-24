<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;
use App\Repositories\MovieInvitationRepositoryInterface;

class MovieInvitationController extends Controller
{
    use JsonResponseTrait;

    protected $movieInvitationRepository;

    public function __construct(MovieInvitationRepositoryInterface $movieInvitationRepository)
    {
        $this->movieInvitationRepository = $movieInvitationRepository;
    }

    public function sendInvitation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'required|exists:users,id',
            'movie_id' => 'required|exists:movies,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $invitations = collect($validated['user_ids'])->map(function ($userId) use ($validated) {
                return [
                    'user_id' => $userId,
                    'invited_by' => auth()->id(),
                    'movie_id' => $validated['movie_id'],
                    'role_id' => $validated['role_id'],
                    'status' => 'pending'
                ];
            });

            $this->movieInvitationRepository->createInvitations($invitations->toArray());

            return $this->successResponse(null, 'Invitations sent successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getPendingInvitations(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $invitations = $this->movieInvitationRepository->getPendingInvitations($userId);
        return $this->successResponse($invitations, 'Pending invitations retrieved');
    }

    public function updateInvitationStatus(Request $request, $id, $status): JsonResponse
    {
        $userId = auth()->id();
        $invitation = $this->movieInvitationRepository->findInvitationByIdAndUser($id, $userId);

        $this->movieInvitationRepository->updateInvitationStatus($invitation, $status);

        if ($status === 'accepted') {
            $this->movieInvitationRepository->createMovieCrew([
                'user_id' => $invitation->user_id,
                'movie_id' => $invitation->project_id,
                'role_id' => $invitation->role_id
            ]);
        }

        return $this->successResponse(null, ucfirst($status) . ' invitation');
    }
}