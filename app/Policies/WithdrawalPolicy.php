<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Withdrawal;

class WithdrawalPolicy
{
    public function viewAny(User $auth): bool               { return $auth->isStaff(); }
    public function view(User $auth, Withdrawal $w): bool   { return $auth->isStaff(); }
    public function create(User $auth): bool                { return false; } // nasabah only via portal
    public function update(User $auth, Withdrawal $w): bool { return $auth->isAdmin(); }
    public function delete(User $auth, Withdrawal $w): bool { return $auth->isAdmin(); }
    public function approve(User $auth, Withdrawal $w): bool { return $auth->isStaff() && $w->status === 'pending'; }
    public function reject(User $auth, Withdrawal $w): bool  { return $auth->isStaff() && $w->status === 'pending'; }
}
