<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Deposit;
use App\Models\PickupRequest;
use App\Models\SiteSetting;
use App\Models\TrashPrice;
use App\Models\User;
use App\Models\Withdrawal;
use App\Policies\ActivityLogPolicy;
use App\Policies\ArticlePolicy;
use App\Policies\DepositPolicy;
use App\Policies\PickupRequestPolicy;
use App\Policies\SiteSettingPolicy;
use App\Policies\TrashPricePolicy;
use App\Policies\UserPolicy;
use App\Policies\WithdrawalPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Deposit::class => DepositPolicy::class,
        Withdrawal::class => WithdrawalPolicy::class,
        PickupRequest::class => PickupRequestPolicy::class,
        Article::class => ArticlePolicy::class,
        TrashPrice::class => TrashPricePolicy::class,
        ActivityLog::class => ActivityLogPolicy::class,
        SiteSetting::class => SiteSettingPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Gate khusus: hanya admin yang bisa atur user
        Gate::define('manage-users', fn (User $user) => $user->isAdmin());
    }
}
