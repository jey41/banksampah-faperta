<?php

namespace App\Policies;

use App\Models\Deposit;
use App\Models\User;

class DepositPolicy
{
    public function viewAny(User $auth): bool
    {
        return $auth->isStaff();
    }

    public function view(User $auth, Deposit $d): bool
    {
        return $auth->isStaff();
    }

    public function create(User $auth): bool
    {
        return $auth->isStaff();
    }

    public function update(User $auth, Deposit $d): bool
    {
        return $auth->isStaff();
    }

    public function delete(User $auth, Deposit $d): bool
    {
        return $auth->isAdmin();
    }

    public function approve(User $auth, Deposit $d): bool
    {
        return $auth->isStaff() && $d->status === 'pending';
    }

    public function reject(User $auth, Deposit $d): bool
    {
        return $auth->isStaff() && $d->status === 'pending';
    }
}
