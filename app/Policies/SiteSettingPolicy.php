<?php

namespace App\Policies;

use App\Models\User;

class SiteSettingPolicy
{
    /**
     * Only super_admin can view site settings.
     */
    public function viewAny(User $auth): bool
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Only super_admin can update site settings.
     */
    public function update(User $auth): bool
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Only super_admin can manage partners.
     */
    public function createPartner(User $auth): bool
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Only super_admin can delete partners.
     */
    public function deletePartner(User $auth): bool
    {
        return $auth->isSuperAdmin();
    }
}
