<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PaymentTransaction>
 */
class PaymentTransactionFactory extends Factory
{
    protected $model = PaymentTransaction::class;

    public function definition(): array
    {
        return [
            'transaction_uuid' => (string) Str::uuid(),
            'customer_id' => Customer::factory(),
            'plan_id' => Plan::factory(),
            'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
            'amount' => 199.00,
            'currency' => 'INR',
            'credits_to_add' => 220,
            'provider_order_id' => 'order_' . Str::random(12),
            'provider_payment_id' => 'pay_' . Str::random(12),
            'status' => PaymentTransaction::STATUS_SUCCESS,
            'verification_status' => PaymentTransaction::VERIFICATION_VERIFIED,
            'paid_at' => now(),
        ];
    }
}
