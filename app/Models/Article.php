<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "blogs".
 *
 * @property int $id
 * @property int $created_by
 * @property int $updated_by
 * @property string $title
 * @property string|null $detail_image
 * @property boolean $active
 * @property boolean $is_important
 * @property string $announcement_text
 * @property string $detail_text
 * @property \DateTime $release_date
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime $deleted_at
 */
class Article extends Model
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
        'announcement_text',
        'detail_text',
        'detail_image',
        'is_important',
        'release_date',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'release_date' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
        'active'       => 'bool',
        'is_important' => 'bool',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
