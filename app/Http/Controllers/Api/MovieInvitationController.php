<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

use App\Models\Movie;
use App\Models\MovieCrew;
use App\Models\MovieInvitation;
use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;


class MovieInvitationController extends Controller
{
    use JsonResponseTrait;
    public function sendInvitation(Request $request): JsonResponse {
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

            MovieInvitation::insert($invitations->toArray());

            return $this->successResponse(null, 'Invitations sent successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getPendingInvitations(Request $request): JsonResponse{
        $invitations = MovieInvitation::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->with('project', 'role')
            ->get();

        return $this->successResponse($invitations, 'Pending invitations retrieved');
    }

    public function updateInvitationStatus(Request $request, $id, $status): JsonResponse{
        $invitation = MovieInvitation::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $invitation->update(['status' => $status]);

        if ($status === 'accepted') {
            // Add user to project crew
            MovieCrew::create([
                'user_id' => $invitation->user_id,
                'movie_id' => $invitation->project_id,
                'role_id' => $invitation->role_id
            ]);
        }

        return $this->successResponse(null, ucfirst($status) . ' invitation');
    }
}