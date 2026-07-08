<?php

namespace App\Policies;

use App\Models\PickupRequest;
use App\Models\User;

class PickupRequestPolicy
{
    public function viewAny(User $auth): bool
    {
        return $auth->isStaff();
    }

    public function view(User $auth, PickupRequest $p): bool
    {
        return $auth->isStaff();
    }

    public function create(User $auth): bool
    {
        return false;
    }

    public function update(User $auth, PickupRequest $p): bool
    {
        return $auth->isStaff();
    }

    public function delete(User $auth, PickupRequest $p): bool
    {
        return $auth->isAdmin();
    }
}
