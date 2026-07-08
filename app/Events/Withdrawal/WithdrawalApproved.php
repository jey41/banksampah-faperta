<?php

namespace App\Events\Withdrawal;

use App\Models\Withdrawal;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WithdrawalApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Withdrawal $withdrawal,
        public User $approver,
        public int $balanceBefore,
        public int $balanceAfter,
    ) {}
}