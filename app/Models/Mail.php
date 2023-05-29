<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * This is the model class for table "mails".
 *
 * @property int $id
 * @property string $theme
 * @property string $username
 * @property string $email
 * @property string $from
 * @property string $to
 * @property string $company
 * @property string $phone
 * @property string $text
 * @property boolean $is_success_sent
 * @property string $attachments
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class Mail extends Model
{
    use HasFactory;

    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'theme',
        'from',
        'to',
        'username',
        'company',
        'phone',
        'text',
        'attachments',
        'is_success_sent',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attachments'     => 'array',
        'is_success_sent' => 'boolean',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
