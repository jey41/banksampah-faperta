<?php

namespace App\Listeners\Withdrawal;

use App\Events\Withdrawal\WithdrawalRejected;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogWithdrawalRejectionActivity implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WithdrawalRejected $event): void
    {
        $withdrawal = $event->withdrawal;
        $rejector = $event->rejector;
        $nasabah = $withdrawal->user;

        ActivityLog::create([
            'user_id' => $rejector->id,
            'action' => 'reject_withdrawal',
            'description' => "{$rejector->name} menolak penarikan #{$withdrawal->id} milik nasabah {$nasabah->name}",
        ]);
    }
}
