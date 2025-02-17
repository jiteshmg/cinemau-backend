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


class MovieController extends Controller
{
    use JsonResponseTrait;
    public function getMoviesByUserId($userId): JsonResponse {
        $movies = Movie::getMoviesByUserId($userId);
        try{
            return $this->successResponse($movies, 'Movies retrieved successfully');
        }catch(Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getMovies(Request $request): JsonResponse {
        $createdBy = $request->query('created_by');

        $movies = Movie::select('id', 'title');

        if ($createdBy) {
            $movies->where('created_by', $createdBy);
        }

        try {
            return $this->successResponse(
                $movies->get(),
                'Movies retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    
}

