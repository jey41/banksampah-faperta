<?php

namespace App\Notifications;

use App\Models\Deposit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositApprovedNotification extends Notification
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
        $donationText = $this->deposit->is_donation ? ' (Donasi)' : '';

        return [
            'type' => 'deposit_approved',
            'title' => 'Setoran Disetujui',
            'message' => "Setoran #{$this->deposit->id}{$donationText} telah disetujui. " .
                ($this->deposit->is_donation
                    ? 'Terima kasih atas donasi Anda!'
                    : "Saldo Anda telah ditambahkan sebesar Rp " . number_format($this->deposit->total_price, 0, ',', '.') . '.'),
            'deposit_id' => $this->deposit->id,
            'amount' => $this->deposit->total_price,
            'is_donation' => $this->deposit->is_donation,
            'link' => "/nasabah/riwayat",
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
