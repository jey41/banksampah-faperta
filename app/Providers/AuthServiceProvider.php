<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\PickupRequest;
use App\Models\Article;
use App\Models\TrashPrice;
use App\Models\ActivityLog;
use App\Models\SiteSetting;

use App\Policies\UserPolicy;
use App\Policies\DepositPolicy;
use App\Policies\WithdrawalPolicy;
use App\Policies\PickupRequestPolicy;
use App\Policies\ArticlePolicy;
use App\Policies\TrashPricePolicy;
use App\Policies\ActivityLogPolicy;
use App\Policies\SiteSettingPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class         => UserPolicy::class,
        Deposit::class      => DepositPolicy::class,
        Withdrawal::class   => WithdrawalPolicy::class,
        PickupRequest::class => PickupRequestPolicy::class,
        Article::class      => ArticlePolicy::class,
        TrashPrice::class   => TrashPricePolicy::class,
        ActivityLog::class  => ActivityLogPolicy::class,
        SiteSetting::class  => SiteSettingPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Gate khusus: hanya admin yang bisa atur user
        Gate::define('manage-users', fn(User $user) => $user->isAdmin());
    }
}
