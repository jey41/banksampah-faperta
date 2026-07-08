<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Article;

class ArticlePolicy
{
    public function viewAny(User $auth): bool             { return $auth->isStaff(); }
    public function view(User $auth, Article $a): bool    { return $auth->isStaff(); }
    public function create(User $auth): bool              { return $auth->isStaff(); }
    public function update(User $auth, Article $a): bool  { return $auth->isStaff(); }
    public function delete(User $auth, Article $a): bool  { return $auth->isAdmin(); }
}
