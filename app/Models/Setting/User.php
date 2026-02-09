<?php

namespace App\Models\Setting;

/**
 * @property string name
 */

use App\Models\PSA\Agent;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string id
 * @property string model_id
 * @property string name
 * @property string email
 * @property string username
 * @property string password
 * @property string description
 * @property string role_id
 * @property string photo
 * @property string status
 * @property string access_locations
 */

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, UUIDAsPrimaryKey, CausesActivity;

    protected $table = 'app_users';
    protected $primaryKey = 'id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'model_id',
        'name',
        'email',
        'username',
        'password',
        'description',
        'role_id',
        'photo',
        'status',
        'access_locations'
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'model_id');
    }
}
