<?php

namespace App\Policies;

use App\Models\Hub;
use App\Models\User;
use App\Enum\UserRole;
use Illuminate\Auth\Access\Response;

class HubPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === UserRole::ADMIN->value) {
            return true;
        }

        return null;
    }
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Hub $hub): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Hub $hub): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Hub $hub): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Hub $hub): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Hub $hub): bool
    {
        return false;
    }
    public function manageServices(User $user, Hub $hub): bool
    {
        return $hub->owner_id === $user->id;
    }
}
