<?php

namespace App\Notifications;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WithdrawalApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public Withdrawal $withdrawal) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $methodLabel = $this->withdrawal->withdrawal_method === 'tunai' ? 'Tunai' : 'Transfer Bank';
        $feeText = $this->withdrawal->admin_fee > 0
            ? ' (termasuk biaya admin Rp '.number_format($this->withdrawal->admin_fee, 0, ',', '.').')'
            : '';

        return [
            'type' => 'withdrawal_approved',
            'title' => 'Penarikan Disetujui',
            'message' => "Penarikan #{$this->withdrawal->id} sebesar Rp ".
                number_format($this->withdrawal->amount, 0, ',', '.').
                " via {$methodLabel} telah disetujui{$feeText}.",
            'withdrawal_id' => $this->withdrawal->id,
            'amount' => $this->withdrawal->amount,
            'admin_fee' => $this->withdrawal->admin_fee,
            'link' => '/nasabah/riwayat',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
