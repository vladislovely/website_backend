<?php

namespace App\Models;

use DateTime;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "success-stories".
 *
 * @property int $id
 * @property int $created_by
 * @property int $updated_by
 * @property string $title
 * @property boolean $active
 * @property string $preview_image
 * @property array $industry
 * @property array $technologies
 * @property array $company
 * @property array $steps
 * @property array $project
 * @property array $similar_cases
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property DateTime $deleted_at
 */
class SuccessStory extends Model
{
    use HasFactory, SoftDeletes;

    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'active',
        'created_by',
        'updated_by',
        'preview_image',
        'industry',
        'technologies',
        'company',
        'steps',
        'project',
        'similar_cases',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
        'active'        => 'bool',
        'industry'      => 'array',
        'technologies'  => 'array',
        'company'       => 'array',
        'steps'         => 'array',
        'project'       => 'array',
        'similar_cases' => 'array',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
