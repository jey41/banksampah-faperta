<?php

namespace App\Listeners\Deposit;

use App\Events\Deposit\DepositRejected;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogDepositRejectionActivity implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(DepositRejected $event): void
    {
        $deposit = $event->deposit;
        $rejector = $event->rejector;
        $nasabah = $deposit->user;

        ActivityLog::create([
            'user_id' => $rejector->id,
            'action' => 'reject_deposit',
            'description' => "{$rejector->name} menolak setoran #{$deposit->id} milik nasabah {$nasabah->name}",
        ]);
    }
}