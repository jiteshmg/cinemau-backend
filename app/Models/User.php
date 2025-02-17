<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\MovieInvitation;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'mobile',
        'email',
        'password',
        'address',
        'image_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
                    
    }

    public function invitations(){
        return $this->hasMany(MovieInvitation::class);
    }

    /**
     * Get the identifier that will be stored in the JWT token.
     * 
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();  // Typically the primary key is used as the identifier
    }

    /**
     * Get the custom claims for the JWT token.
     * 
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];  // You can add any custom claims if needed
    }
}
