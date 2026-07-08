<?php

namespace App\Listeners\Deposit;

use App\Events\Deposit\DepositApproved;
use App\Services\GamificationService;

class SyncUserBadgesAfterDeposit
{
    public function handle(DepositApproved $event): void
    {
        $nasabah = $event->deposit->user;
        if ($nasabah) {
            app(GamificationService::class)->syncBadges($nasabah);
        }
    }
}