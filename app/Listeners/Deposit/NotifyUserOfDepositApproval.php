<?php

namespace App\Listeners\Deposit;

use App\Events\Deposit\DepositApproved;
use App\Notifications\DepositApprovedNotification;

class NotifyUserOfDepositApproval
{
    public function handle(DepositApproved $event): void
    {
        $nasabah = $event->deposit->user;
        if ($nasabah) {
            $nasabah->notify(new DepositApprovedNotification($event->deposit));
        }
    }
}
