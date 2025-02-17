<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Project;
use App\Models\Role;
class MovieInvitation extends Model
{
    protected $fillable = [
        'user_id',
        'invited_by',
        'project_id',
        'role_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}