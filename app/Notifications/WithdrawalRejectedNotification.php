<?php

namespace App\Notifications;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WithdrawalRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public Withdrawal $withdrawal) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'withdrawal_rejected',
            'title' => 'Penarikan Ditolak',
            'message' => "Penarikan #{$this->withdrawal->id} sebesar Rp ".
                number_format($this->withdrawal->amount, 0, ',', '.').
                ' telah ditolak. '.
                ($this->withdrawal->notes
                    ? "Alasan: {$this->withdrawal->notes}"
                    : 'Silakan hubungi admin untuk informasi lebih lanjut.'),
            'withdrawal_id' => $this->withdrawal->id,
            'amount' => $this->withdrawal->amount,
            'link' => '/nasabah/riwayat',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
