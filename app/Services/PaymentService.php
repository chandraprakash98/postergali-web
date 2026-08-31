<?php

namespace App\Services;

use App\DTOs\PaymentData;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Validate payment business rules using exact decimal arithmetic.
     *
     * @throws ValidationException
     */
    public function validatePaymentRules(PaymentData $paymentData): void
    {
        $total = number_format((float) $paymentData->totalAmount, 2, '.', '');
        $razorpay = number_format((float) $paymentData->razorpayAmount, 2, '.', '');
        $credit = number_format((float) $paymentData->creditAmount, 2, '.', '');
        $type = strtoupper($paymentData->paymentType);

        if (bccomp($total, '0.00', 2) < 0) {
            throw ValidationException::withMessages([
                'total_amount' => ['Total amount cannot be negative.'],
            ]);
        }

        switch ($type) {
            case Payment::TYPE_FULL_UPI:
                // Rule 1: total_amount = razorpay_amount
                if (bccomp($total, $razorpay, 2) !== 0) {
                    throw ValidationException::withMessages([
                        'razorpay_amount' => ["For FULL_UPI, razorpay_amount ($razorpay) must equal total_amount ($total)."],
                    ]);
                }
                // Rule 2: credit_amount = 0
                if (bccomp($credit, '0.00', 2) !== 0) {
                    throw ValidationException::withMessages([
                        'credit_amount' => ["For FULL_UPI, credit_amount must be 0. Received: $credit."],
                    ]);
                }
                break;

            case Payment::TYPE_SEMI:
                // Rule 1: razorpay_amount > 0
                if (bccomp($razorpay, '0.00', 2) <= 0) {
                    throw ValidationException::withMessages([
                        'razorpay_amount' => ['For SEMI payment, razorpay_amount must be greater than 0.'],
                    ]);
                }
                // Rule 2: credit_amount > 0
                if (bccomp($credit, '0.00', 2) <= 0) {
                    throw ValidationException::withMessages([
                        'credit_amount' => ['For SEMI payment, credit_amount must be greater than 0.'],
                    ]);
                }
                // Rule 3: razorpay_amount + credit_amount = total_amount
                $sum = bcadd($razorpay, $credit, 2);
                if (bccomp($sum, $total, 2) !== 0) {
                    throw ValidationException::withMessages([
                        'total_amount' => ["For SEMI payment, razorpay_amount ($razorpay) + credit_amount ($credit) must equal total_amount ($total). Sum is $sum."],
                    ]);
                }
                break;

            case Payment::TYPE_FULL_CREDIT:
                // Rule 1: credit_amount = total_amount
                if (bccomp($credit, $total, 2) !== 0) {
                    throw ValidationException::withMessages([
                        'credit_amount' => ["For FULL_CREDIT, credit_amount ($credit) must equal total_amount ($total)."],
                    ]);
                }
                // Rule 2: razorpay_amount = 0
                if (bccomp($razorpay, '0.00', 2) !== 0) {
                    throw ValidationException::withMessages([
                        'razorpay_amount' => ["For FULL_CREDIT, razorpay_amount must be 0. Received: $razorpay."],
                    ]);
                }
                break;

            default:
                throw ValidationException::withMessages([
                    'payment_type' => ["Unsupported payment type: $type. Must be one of FULL_UPI, SEMI, FULL_CREDIT."],
                ]);
        }
    }

    /**
     * Process payment, apply business rules, deduct credits if needed, and create payment record.
     */
    public function processPayment(PaymentData $paymentData): Payment
    {
        $this->validatePaymentRules($paymentData);

        return DB::transaction(function () use ($paymentData) {
            $customerId = $paymentData->customerId;
            if (!$customerId && !empty($paymentData->mobile)) {
                $customerId = Customer::where('mobile', (string) $paymentData->mobile)->value('customer_id');
            }

            // Deduct customer credit if applicable
            $creditAmount = $paymentData->creditAmount;
            if ($customerId && bccomp($creditAmount, '0.00', 2) > 0) {
                $this->deductCustomerCredit($customerId, $creditAmount);
            }

            $paymentAttributes = $paymentData->toArray();
            if ($customerId) {
                $paymentAttributes['customer_id'] = $customerId;
            }

            // Create Payment record
            return Payment::create($paymentAttributes);
        });
    }

    /**
     * Deduct customer credit with precision.
     */
    public function deductCustomerCredit(string $customerId, string $amountToDeduct): CustomerCredit
    {
        $credit = CustomerCredit::where('customer_id', $customerId)->lockForUpdate()->first();

        if (!$credit) {
            $credit = CustomerCredit::create([
                'customer_id' => $customerId,
                'balance' => 1000.00,
            ]);
        }

        $currentBalance = number_format((float) $credit->balance, 2, '.', '');
        $newBalance = bcsub($currentBalance, $amountToDeduct, 2);

        // Don't go below 0
        if (bccomp($newBalance, '0.00', 2) < 0) {
            $newBalance = '0.00';
        }

        $credit->balance = (float) $newBalance;
        $credit->save();

        return $credit;
    }
}
