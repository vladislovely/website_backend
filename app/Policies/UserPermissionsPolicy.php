<?php

namespace App\Policies;

use App\Models\User;

class UserPermissionsPolicy
{
    public function update(User $user): bool
    {
        return $user->tokenCan('update-user-permissions');
    }
}
