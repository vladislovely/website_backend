<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "vacancies".
 *
 * @property int $id
 * @property int $created_by
 * @property int $updated_by
 * @property string $title
 * @property boolean $active
 * @property string $announcement_text
 * @property string|null $detail_text
 * @property string|null $banner_image
 * @property string $description
 * @property array $conditions
 * @property array|null $locations
 * @property string|null $language_level
 * @property string|null $grade
 * @property string|null $country
 * @property boolean $remote_format
 * @property array $technologies
 * @property array $specialisations
 * @property array|null $offer_timeline
 * @property string $vacancy_type
 * @property string $work_schedule
 * @property string $type_of_employment
 * @property string $work_experience
 * @property array|null $salary
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime $deleted_at
 */
class Vacancy extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'created_by',
        'updated_by',
        'active',
        'announcement_text',
        'detail_text',
        'banner_image',
        'description',
        'conditions',
        'locations',
        'language_level',
        'grade',
        'country',
        'remote_format',
        'technologies',
        'specialisations',
        'offer_timeline',
        'vacancy_type',
        'work_schedule',
        'type_of_employment',
        'work_experience',
        'salary',
    ];

    protected $casts = [
        'conditions'         => 'array',
        'locations'          => 'array',
        'language_level'     => 'array',
        'grade'              => 'array',
        'country'            => 'array',
        'technologies'       => 'array',
        'specialisations'    => 'array',
        'offer_timeline'     => 'array',
        'vacancy_type'       => 'array',
        'work_schedule'      => 'array',
        'type_of_employment' => 'array',
        'work_experience'    => 'array',
        'salary'             => 'array',
        'active'             => 'bool',
        'remote_format'      => 'bool',
    ];


    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
