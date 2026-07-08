<?php

namespace App\Events\Deposit;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DepositApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Deposit $deposit,
        public User $approver,
        public int $balanceBefore,
        public int $balanceAfter,
    ) {}
}
