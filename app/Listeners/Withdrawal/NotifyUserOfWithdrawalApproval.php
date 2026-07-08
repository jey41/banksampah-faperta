<?php

namespace App\Listeners\Withdrawal;

use App\Events\Withdrawal\WithdrawalApproved;
use App\Notifications\WithdrawalApprovedNotification;

class NotifyUserOfWithdrawalApproval
{
    public function handle(WithdrawalApproved $event): void
    {
        $nasabah = $event->withdrawal->user;
        if ($nasabah) {
            $nasabah->notify(new WithdrawalApprovedNotification($event->withdrawal));
        }
    }
}
