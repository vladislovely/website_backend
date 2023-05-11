<?php

namespace App\Policies;

use App\Models\SuccessStory;
use App\Models\User;

class SuccessStoriesPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdministrator()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->tokenCan('create-success-story');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SuccessStory $model): bool
    {
        return $user->id === $model->created_by || $user->tokenCan('update-success-story');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SuccessStory $model): bool
    {
        return $user->id === $model->created_by || $user->tokenCan('delete-success-story');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SuccessStory $model): bool
    {
        return $user->tokenCan('restore-success-story');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SuccessStory $model): bool
    {
        return $user->tokenCan('permanently-delete-success-story');
    }
}
