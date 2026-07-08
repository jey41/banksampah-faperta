<?php

namespace App\Listeners\Withdrawal;

use App\Events\Withdrawal\WithdrawalApproved;

/**
 * Generate withdrawal receipt for approved withdrawals.
 * Currently a no-op - implement when receipt generation is needed.
 */
class GenerateWithdrawalReceipt
{
    public function handle(WithdrawalApproved $event): void
    {
        // TODO: Generate PDF receipt for withdrawal
    }
}
