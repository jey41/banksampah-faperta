<?php

namespace App\Listeners\Deposit;

use App\Events\Deposit\DepositApproved;

/**
 * NOTE: Mutation is already recorded in TransactionService::approveDeposit().
 * This listener is kept as a no-op to avoid breaking the event listener registration.
 * If you need to add additional side effects on deposit approval, add them here.
 */
class RecordDepositMutation
{
    public function handle(DepositApproved $event): void
    {
        // Mutation already recorded in TransactionService
    }
}
