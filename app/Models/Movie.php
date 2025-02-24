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

    protected $dates = [
        'release_date',
        'expected_start_date',
    ];

    protected $casts = [
        'created_by' => 'integer',
        'rating' => 'decimal:1',
        'budget' => 'decimal:2',
        'box_office' => 'decimal:2',
    ];
    public function movieCrew()
    {
        return $this->hasMany(MovieCrew::class, 'movie_id');
    }

    public static function getMoviesByUserId($userId){
        return self::where(function ($query) use ($userId) {
            $query->where('created_by', $userId)
                ->orWhereHas('movieCrew', function ($subquery) use ($userId) {
                    $subquery->where('user_id', $userId);
                });
        })
        ->with(['movieCrew.user:id,name', 'movieCrew.role:id,name'])
        ->get();
    }

    public function invitations(){
        return $this->hasMany(MovieInvitation::class);
    }
}