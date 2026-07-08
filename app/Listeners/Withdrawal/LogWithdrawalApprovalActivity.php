<?php

namespace App\Listeners\Withdrawal;

use App\Events\Withdrawal\WithdrawalApproved;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogWithdrawalApprovalActivity implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WithdrawalApproved $event): void
    {
        $withdrawal = $event->withdrawal;
        $approver = $event->approver;
        $nasabah = $withdrawal->user;

        ActivityLog::create([
            'user_id' => $approver->id,
            'action' => 'approve_withdrawal',
            'description' => "{$approver->name} menyetujui penarikan #{$withdrawal->id} milik nasabah {$nasabah->name} sejumlah Rp ".number_format($withdrawal->amount, 0, ',', '.'),
        ]);
    }
}
