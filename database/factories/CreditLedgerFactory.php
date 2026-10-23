<?php

namespace Database\Factories;

use App\Models\CreditLedger;
use App\Models\Customer;
use App\Models\PaymentTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CreditLedger>
 */
class CreditLedgerFactory extends Factory
{
    protected $model = CreditLedger::class;

    public function definition(): array
    {
        return [
            'ledger_uuid' => (string) Str::uuid(),
            'customer_id' => Customer::factory(),
            'payment_transaction_id' => PaymentTransaction::factory(),
            'entry_type' => CreditLedger::TYPE_CREDIT,
            'transaction_category' => CreditLedger::CATEGORY_WALLET_TOPUP,
            'credit_amount' => 220.00,
            'debit_amount' => 0.00,
            'balance_before' => 1000.00,
            'balance_after' => 1220.00,
            'reference_type' => 'payment_transaction',
            'reference_id' => (string) Str::uuid(),
            'description' => 'Credit Top-up: Value Pack (220 credits)',
            'created_by' => 'system',
            'created_at' => now(),
        ];
    }
}
