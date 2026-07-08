<?php

namespace App\Listeners\Deposit;

use App\Events\Deposit\DepositApproved;
use App\Models\ActivityLog;

class LogDepositApprovalActivity
{
    public function handle(DepositApproved $event): void
    {
        $deposit = $event->deposit;
        $approver = $event->approver;
        $nasabah = $deposit->user;

        $categoryText = $deposit->donation_category
            ? ' - Kategori: ' . ($deposit->donation_category === 'umum' ? 'Sampah Umum' : 'Sampah Donasi')
            : '';

        $donationText = $deposit->is_donation ? ' [DONASI]' : '';

        ActivityLog::create([
            'user_id' => $approver->id,
            'action' => 'approve_deposit',
            'description' => "{$approver->name} menyetujui setoran #{$deposit->id} milik nasabah {$nasabah->name}{$donationText}{$categoryText} dengan total berat {$deposit->total_weight} kg/L dan total nilai Rp " . number_format($deposit->total_price, 0, ',', '.'),
        ]);
    }
}