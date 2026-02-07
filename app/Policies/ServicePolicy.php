<?php

namespace App\Policies;

use App\Models\Hub;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Service;
use App\Traits\HandlesOwnership;

class ServicePolicy
{
    use HandlesOwnership;
    /**
     * Admin bypass (أسرع + أنظف)
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === UserRole::ADMIN->value) {
            return true;
        }

        return null;
    }

    /**
     * عرض الخدمات (public)
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Service $service): bool
    {
        return true;
    }

    /**
     * إنشاء خدمة
     */
    public function create(User $user, Hub $hub): bool
    {

        return $this->isOwner($user, $hub->owner_id);

    }

    /**
     * تعديل خدمة
     */
    public function update(User $user, Service $service): bool
    {
        return $this->isOwner($user, $service->hub()->value('owner_id'));
    }

    /**
     * حذف خدمة
     */
    public function delete(User $user, Service $service): bool
    {
        return $this->isOwner($user, $service->hub()->value('owner_id'));
    }
}
