<?php

namespace App\Repositories;

use App\Models\Movie;
use App\Models\MovieCrew;

class MovieRepository implements MovieRepositoryInterface
{
    public function getMoviesByUserId($userId)
    {
        return Movie::getMoviesByUserId($userId); // Assuming this is a custom scope or static method
    }

    public function getAllMovies(?int $createdBy = null)
    {
        $query = Movie::select('id', 'title');
        
        if ($createdBy) {
            $query->where('created_by', $createdBy);
        }

        return $query->get();
    }

    public function createMovie(array $data)
    {
        return Movie::create($data);
    }

    public function createMovieCrew(array $data)
    {
        return MovieCrew::create($data);
    }
}