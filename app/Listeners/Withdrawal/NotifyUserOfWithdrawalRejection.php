<?php

namespace App\Listeners\Withdrawal;

use App\Events\Withdrawal\WithdrawalRejected;
use App\Notifications\WithdrawalRejectedNotification;

class NotifyUserOfWithdrawalRejection
{
    public function handle(WithdrawalRejected $event): void
    {
        $nasabah = $event->withdrawal->user;
        if ($nasabah) {
            $nasabah->notify(new WithdrawalRejectedNotification($event->withdrawal));
        }
    }
}
