<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use App\Models\Vacancy;

class ArticlePolicy
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
        return $user->tokenCan('create-article');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Article $article): bool
    {
        return $user->id === $article->created_by || $user->tokenCan('update-article');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): bool
    {
        return $user->id === $article->created_by || $user->tokenCan('delete-article');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Vacancy $model): bool
    {
        return $user->tokenCan('restore-article');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Vacancy $model): bool
    {
        return $user->tokenCan('permanently-delete-article');
    }
}
