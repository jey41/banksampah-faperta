<?php

namespace App\Notifications;

use App\Models\Deposit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DepositRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public Deposit $deposit)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'deposit_rejected',
            'title' => 'Setoran Ditolak',
            'message' => "Setoran #{$this->deposit->id} telah ditolak. " .
                ($this->deposit->notes
                    ? "Alasan: {$this->deposit->notes}"
                    : 'Silakan hubungi admin untuk informasi lebih lanjut.'),
            'deposit_id' => $this->deposit->id,
            'link' => "/nasabah/riwayat",
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
