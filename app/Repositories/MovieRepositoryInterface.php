<?php

namespace App\Repositories;

interface MovieRepositoryInterface
{
    public function getMoviesByUserId($userId);
    public function getAllMovies(?int $createdBy = null);
    public function createMovie(array $data);
    public function createMovieCrew(array $data);
}