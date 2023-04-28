<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laragear\TwoFactor\TwoFactorAuthentication;
use Laragear\TwoFactor\Contracts\TwoFactorAuthenticatable;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $status
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class User extends Authenticatable implements TwoFactorAuthenticatable
{
    use TwoFactorAuthentication, HasFactory, Notifiable, SoftDeletes;

    public const ADMIN_MAIL = 'admin@sibedge.com';
    protected $dateFormat = 'Y-m-d H:i:s';
    public const DEFAULT_ABILITIES = [
        'create-vacancy',
        'update-vacancy',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'status',
        'deleted_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(Ability::class, 'user_abilities');
    }

    public function isAdministrator(): bool
    {
        return $this->email === self::ADMIN_MAIL;
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
