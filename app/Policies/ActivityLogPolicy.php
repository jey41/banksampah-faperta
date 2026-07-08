<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ActivityLog;

class ActivityLogPolicy
{
    public function viewAny(User $auth): bool                { return $auth->isStaff(); }
    public function view(User $auth, ActivityLog $l): bool   { return $auth->isStaff(); }
    public function create(User $auth): bool                 { return false; }
    public function update(User $auth, ActivityLog $l): bool { return false; }
    public function delete(User $auth, ActivityLog $l): bool { return false; }
}
