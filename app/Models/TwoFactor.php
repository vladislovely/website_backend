<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TwoFactor extends Model
{
    use HasFactory;

    protected $table = 'two_factor_authentications';
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'id',
        'authenticatable_type',
        'authenticatable_id',
        'enabled_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'enabled_at'        => 'datetime',
    ];

    public function twoFactor(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'authenticatable_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
