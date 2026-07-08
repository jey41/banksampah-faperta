<?php

namespace App\Policies;

use App\Models\TrashPrice;
use App\Models\User;

class TrashPricePolicy
{
    public function viewAny(User $auth): bool
    {
        return $auth->isStaff();
    }

    public function view(User $auth, TrashPrice $t): bool
    {
        return $auth->isStaff();
    }

    public function create(User $auth): bool
    {
        return $auth->isStaff();
    }

    public function update(User $auth, TrashPrice $t): bool
    {
        return $auth->isStaff();
    }

    public function delete(User $auth, TrashPrice $t): bool
    {
        return $auth->isAdmin();
    }
}
