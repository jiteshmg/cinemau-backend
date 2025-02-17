<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Role;

class MovieCrew extends Model
{
    use HasFactory;

    protected $table = 'movie_crew';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['movie_id', 'user_id', 'role_id'];

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
