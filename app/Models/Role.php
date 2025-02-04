<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Role extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
    ];

    /**
     * Get the user roles associated with the role.
     */
    public function userRoles()    {
        return $this->hasMany(UserRole::class, 'role_id', 'id');
    }

    /**
     * Get the movie crew members associated with the role.
     */
    public function movieCrew()    {
        return $this->hasMany(MovieCrew::class, 'role_id', 'id');
    }

    public function users()
{
    return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
                
}
}