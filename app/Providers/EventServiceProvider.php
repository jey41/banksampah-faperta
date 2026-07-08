<?php

namespace App\Providers;

use App\Events\Deposit\DepositApproved;
use App\Events\Deposit\DepositRejected;
use App\Events\Withdrawal\WithdrawalApproved;
use App\Events\Withdrawal\WithdrawalRejected;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        DepositApproved::class => [
            'App\Listeners\Deposit\RecordDepositMutation',
            'App\Listeners\Deposit\LogDepositApprovalActivity',
            'App\Listeners\Deposit\SyncUserBadgesAfterDeposit',
            'App\Listeners\Deposit\NotifyUserOfDepositApproval',
        ],

        DepositRejected::class => [
            'App\Listeners\Deposit\LogDepositRejectionActivity',
            'App\Listeners\Deposit\NotifyUserOfDepositRejection',
        ],

        WithdrawalApproved::class => [
            'App\Listeners\Withdrawal\RecordWithdrawalMutation',
            'App\Listeners\Withdrawal\LogWithdrawalApprovalActivity',
            'App\Listeners\Withdrawal\NotifyUserOfWithdrawalApproval',
            'App\Listeners\Withdrawal\GenerateWithdrawalReceipt',
        ],

        WithdrawalRejected::class => [
            'App\Listeners\Withdrawal\LogWithdrawalRejectionActivity',
            'App\Listeners\Withdrawal\NotifyUserOfWithdrawalRejection',
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
