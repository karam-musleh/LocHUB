<?php

namespace App\Traits;

use App\Models\User;
use App\Enum\UserRole;

trait HandlesOwnership
{
    //
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === UserRole::ADMIN->value) {
            return true;
        }

        return null;
    }

    protected function isOwner(User $user, int $ownerId): bool
    {
        return (int) $user->id === (int) $ownerId;
    }
}
