<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Ability
 *
 * @property int $id
 * @property string $name
 */
class Ability extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}
