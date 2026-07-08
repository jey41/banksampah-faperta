<?php

namespace App\Listeners\Deposit;

use App\Events\Deposit\DepositRejected;
use App\Notifications\DepositRejectedNotification;

class NotifyUserOfDepositRejection
{
    public function handle(DepositRejected $event): void
    {
        $nasabah = $event->deposit->user;
        if ($nasabah) {
            $nasabah->notify(new DepositRejectedNotification($event->deposit));
        }
    }
}
