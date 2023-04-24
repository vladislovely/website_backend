<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static STATUS_VERIFIED()
 * @method static static STATUS_NOT_VERIFIED()
 * @method static static OptionOne()
 * @method static static OptionTwo()
 */
final class UserStatus extends Enum
{
    public const STATUS_VERIFIED     = 0;
    public const STATUS_NOT_VERIFIED = 1;


    public function toArray(): UserStatus
    {
        return $this;
    }
}
