<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\MovieCrew;
use App\Models\MovieInvitation;

class Movie extends Model
{
    use HasFactory;

    protected $table = 'movies';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'title',
        'description',
        'release_date',
        'genre',
        'duration_minutes',
        'rating',
        'budget',
        'box_office',
        'poster_url',
        'trailer_url',
        'status',
        'expected_start_date'
    ];
    public function movieCrew()
    {
        return $this->hasMany(MovieCrew::class, 'movie_id');
    }

    public static function getMoviesByUserId($userId)
    {
        return self::whereHas('movieCrew', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with(['movieCrew.user:id,name','movieCrew.role:id,name']) // Eager load user's name
        ->get();
    }

    public function invitations(){
        return $this->hasMany(MovieInvitation::class);
    }
}