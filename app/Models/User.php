<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $status
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public const ADMIN_MAIL = 'admin@sibedge.com';
    public const DEFAULT_ABILITIES = [
        'update-user',
        'view-vacancies',
        'view-vacancy',
        'create-vacancy',
        'update-vacancy',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'last_name',
        'email',
        'password',
        'status',
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
        'status' => UserStatus::class,
    ];

    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(Ability::class, 'user_abilities');
    }

    public function isAdministrator(): bool {
        return $this->email === self::ADMIN_MAIL;
    }
}
