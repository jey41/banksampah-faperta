<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /** Hanya admin yang boleh kelola akun pengguna. */
    public function viewAny(User $auth): bool  { return $auth->isStaff(); }
    public function view(User $auth, User $model): bool { return $auth->isStaff(); }
    public function create(User $auth): bool   { return $auth->isAdmin(); }
    public function update(User $auth, User $model): bool 
    { 
        return $auth->isAdmin();
    }
    public function delete(User $auth, User $model): bool 
    { 
        return $auth->isAdmin() && $auth->id !== $model->id;
    }
}
