<?php

namespace App\Services;

use App\Models\CreditLedger;
use App\Models\Customer;
use App\Models\CustomerCredit;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CreditLedgerService
{
    public function credit(
        string $customerId,
        string|int|float $amount,
        string $description,
        string $category = CreditLedger::CATEGORY_BONUS,
        ?string $referenceType = null,
        ?string $referenceId = null,
        ?string $idempotencyKey = null,
        ?string $createdBy = null,
    ): CreditLedger {
        return $this->apply(
            customerId: $customerId,
            amount: $amount,
            entryType: CreditLedger::TYPE_CREDIT,
            category: $category,
            description: $description,
            referenceType: $referenceType,
            referenceId: $referenceId,
            idempotencyKey: $idempotencyKey,
            createdBy: $createdBy,
        );
    }

    public function debit(
        string $customerId,
        string|int|float $amount,
        string $description,
        string $category = CreditLedger::CATEGORY_POSTER_PURCHASE,
        ?string $referenceType = null,
        ?string $referenceId = null,
        ?string $idempotencyKey = null,
        ?string $createdBy = null,
    ): CreditLedger {
        return $this->apply(
            customerId: $customerId,
            amount: $amount,
            entryType: CreditLedger::TYPE_DEBIT,
            category: $category,
            description: $description,
            referenceType: $referenceType,
            referenceId: $referenceId,
            idempotencyKey: $idempotencyKey,
            createdBy: $createdBy,
        );
    }

    private function apply(
        string $customerId,
        string|int|float $amount,
        string $entryType,
        string $category,
        string $description,
        ?string $referenceType,
        ?string $referenceId,
        ?string $idempotencyKey,
        ?string $createdBy,
    ): CreditLedger {
        $amount = number_format((float) $amount, 2, '.', '');
        if (bccomp($amount, '0.00', 2) < 0) {
            throw new InvalidArgumentException('Wallet credit or debit amount cannot be negative.');
        }

        return DB::transaction(function () use (
            $customerId,
            $amount,
            $entryType,
            $category,
            $description,
            $referenceType,
            $referenceId,
            $idempotencyKey,
            $createdBy
        ): CreditLedger {
            Customer::where('customer_id', $customerId)->lockForUpdate()->firstOrFail();

            if ($idempotencyKey !== null) {
                $existingEntry = CreditLedger::where('idempotency_key', $idempotencyKey)->first();
                if ($existingEntry) {
                    return $existingEntry;
                }
            }

            $wallet = CustomerCredit::where('customer_id', $customerId)
                ->lockForUpdate()
                ->first();

            if (! $wallet) {
                $wallet = CustomerCredit::create([
                    'customer_id' => $customerId,
                    'balance' => 0,
                ]);
            }

            $balanceBefore = number_format((float) $wallet->balance, 2, '.', '');
            $amountApplied = $amount;
            if ($entryType === CreditLedger::TYPE_DEBIT && bccomp($amountApplied, $balanceBefore, 2) > 0) {
                $amountApplied = $balanceBefore;
            }

            $balanceAfter = $entryType === CreditLedger::TYPE_CREDIT
                ? bcadd($balanceBefore, $amountApplied, 2)
                : bcsub($balanceBefore, $amountApplied, 2);

            $entry = CreditLedger::create([
                'customer_id' => $customerId,
                'entry_type' => $entryType,
                'transaction_category' => $category,
                'credit_amount' => $entryType === CreditLedger::TYPE_CREDIT ? $amountApplied : 0,
                'debit_amount' => $entryType === CreditLedger::TYPE_DEBIT ? $amountApplied : 0,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description,
                'idempotency_key' => $idempotencyKey,
                'created_by' => $createdBy,
            ]);

            $wallet->balance = $balanceAfter;
            $wallet->save();

            return $entry;
        });
    }
}
