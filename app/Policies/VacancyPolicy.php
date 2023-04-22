<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class VacancyPolicy
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
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->tokenCan('view-vacancies');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vacancy $vacancy): bool
    {
        return $user->tokenCan('view-vacancy');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->tokenCan('create-vacancy');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vacancy $vacancy): bool
    {
        return $user->id === $vacancy->created_by && $user->tokenCan('update-vacancy');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vacancy $vacancy): bool
    {
        \Log::error('police delete', [$vacancy]);
        \Log::error('can by id', [$user->id === $vacancy->created_by]);
        \Log::error('user', [$user]);
        \Log::error('can by token permanently-delete-vacancy', [$user->tokenCan('permanently-delete-vacancy')]);
        \Log::error('can by token delete', [$user->tokenCan('delete-vacancy')]);
        \Log::error('can by token restore', [$user->tokenCan('restore-vacancy')]);

        return $user->id === $vacancy->created_by && $user->tokenCan('delete-vacancy');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Vacancy $vacancy): bool
    {
        return $user->tokenCan('restore-vacancy');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Vacancy $vacancy): bool
    {
        return $user->tokenCan('permanently-delete-vacancy');
    }
}
