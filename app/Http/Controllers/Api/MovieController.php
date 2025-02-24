<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Traits\JsonResponseTrait;
use App\Http\Controllers\Controller;
use App\Repositories\MovieRepositoryInterface;

class MovieController extends Controller
{
    use JsonResponseTrait;

    protected $movieRepository;

    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    /**
     * @param int $userId
     * @return JsonResponse
     */
    public function getMoviesByUserId($userId): JsonResponse
    {
        try {
            $movies = $this->movieRepository->getMoviesByUserId($userId);
            return $this->successResponse($movies, 'Movies retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMovies(Request $request): JsonResponse
    {
        $createdBy = $request->query('created_by');

        try {
            $movies = $this->movieRepository->getAllMovies($createdBy);
            return $this->successResponse($movies, 'Movies retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createMovie(Request $request): JsonResponse
    {
        // Convert camelCase request keys to snake_case
        $snakeData = [];
        foreach ($request->all() as $key => $value) {
            $snakeKey = Str::snake($key);
            $snakeData[$snakeKey] = $value;
        }

        // Validate the snake_case data
        $validator = Validator::make($snakeData, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
            'genre' => 'nullable|string|max:100',
            'duration_minutes' => 'nullable|integer|min:0',
            'rating' => 'nullable|numeric|min:0.0|max:10.0',
            'budget' => 'nullable|numeric|min:0',
            'box_office' => 'nullable|numeric|min:0',
            'poster_url' => 'nullable|string|max:255',
            'trailer_url' => 'nullable|string|max:255',
            'status' => 'required|in:Ongoing,Completed,Upcoming',
            'expected_start_date' => 'nullable|date',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $validatedData = $validator->validated();

        // Authenticate the user and assign creator ID
        $userId = auth()->id();

        if (!$userId) {
            return $this->errorResponse('Unauthorized', 401);
        }

        // Prepare movie data excluding role_ids
        $movieData = array_merge(
            $validatedData,
            ['created_by' => $userId]
        );

        // Remove role_ids from main movie data
        $roleIds = $validatedData['role_ids'] ?? [];
        unset($movieData['role_ids']);

        try {
            // Create the movie
            $movie = $this->movieRepository->createMovie($movieData);

            // Assign roles to the movie_crew table
            foreach ($roleIds as $roleId) {
                $this->movieRepository->createMovieCrew([
                    'movie_id' => $movie->id,
                    'user_id' => $userId,
                    'role_id' => $roleId,
                ]);
            }

            return $this->successResponse($movie, 'Movie created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}