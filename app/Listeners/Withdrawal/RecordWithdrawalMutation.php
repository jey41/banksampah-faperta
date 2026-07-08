<?php

namespace App\Listeners\Withdrawal;

use App\Events\Withdrawal\WithdrawalApproved;

/**
 * NOTE: Mutation is already recorded in TransactionService::approveWithdrawal().
 * This listener is kept as a no-op to avoid breaking the event listener registration.
 * If you need to add additional side effects on withdrawal approval, add them here.
 */
class RecordWithdrawalMutation
{
    public function handle(WithdrawalApproved $event): void
    {
        // Mutation already recorded in TransactionService
    }
}
