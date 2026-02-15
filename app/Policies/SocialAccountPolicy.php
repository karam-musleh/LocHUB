<?php

namespace App\Policies;

use App\Enum\UserRole;
use App\Models\Hub;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SocialAccountPolicy
{


    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === UserRole::ADMIN->value) {
            return true;
        }

        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function create(User $user, Hub $hub): bool
    {
        // فقط صاحب الـ Hub يمكنه الإضافة
        return $hub->owner_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SocialAccount $socialAccount): bool
    {
        // فقط صاحب الـ Hub الذي يملك الحساب
        return $socialAccount->hub->owner_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SocialAccount $socialAccount): bool
    {
        // فقط صاحب الـ Hub الذي يملك الحساب
        return $socialAccount->hub->owner_id === $user->id;
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SocialAccount $socialAccount): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SocialAccount $socialAccount): bool
    {
        return false;
    }
}
