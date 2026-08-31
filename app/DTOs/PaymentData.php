<?php

namespace App\DTOs;

use App\Models\Customer;
use App\Models\CustomerCredit;

class PaymentData
{
    public function __construct(
        public readonly string $paymentType,
        public readonly string $totalAmount,
        public readonly string $razorpayAmount,
        public readonly string $creditAmount,
        public readonly ?string $razorpayOrderId = null,
        public readonly ?string $razorpayPaymentId = null,
        public readonly string $paymentStatus = 'COMPLETED',
        public readonly ?string $transactionId = null,
        public readonly ?string $itemType = null,
        public readonly ?int $jobOrOfferId = null,
        public readonly ?string $customerId = null,
        public readonly ?string $rawCreditMode = null,
        public readonly ?string $mobile = null,
    ) {}

    /**
     * Create PaymentData from array with default inference and backwards compatibility
     */
    public static function fromArray(array $data, ?string $itemType = null, ?int $jobOrOfferId = null): self
    {
        $hasExplicitPaymentType = isset($data['payment_type']);
        $rawCreditMode = $data['credit_mode'] ?? null;
        $rawPaymentType = $data['payment_type'] ?? $rawCreditMode ?? 'FULL_UPI';
        $normalizedPaymentType = strtoupper(trim((string) $rawPaymentType));

        if (!in_array($normalizedPaymentType, ['FULL_UPI', 'SEMI', 'FULL_CREDIT'], true)) {
            $normalizedPaymentType = 'FULL_UPI';
        }

        // Determine total amount
        $rawTotal = $data['total_amount'] ?? $data['amount'] ?? $data['salary'] ?? '0.00';
        $totalAmount = number_format((float) $rawTotal, 2, '.', '');
        
        $mobile = $data['mobile'] ?? $data['phone_number'] ?? $data['mobile_number'] ?? $data['phone'] ?? null;
        $customerId = $data['customer_id'] ?? null;

        if ($mobile) {
            $customer = Customer::where('mobile', (string) $mobile)->first();
            if ($customer) {
                $customerId = $customer->customer_id;
            }
        }

        // If explicit amounts are passed, use them
        if (isset($data['razorpay_amount']) || isset($data['credit_amount'])) {
            $razorpayAmount = isset($data['razorpay_amount'])
                ? number_format((float) $data['razorpay_amount'], 2, '.', '')
                : number_format((float) bcsub($totalAmount, (string) (float) $data['credit_amount'], 2), 2, '.', '');

            $creditAmount = isset($data['credit_amount'])
                ? number_format((float) $data['credit_amount'], 2, '.', '')
                : number_format((float) bcsub($totalAmount, (string) (float) $data['razorpay_amount'], 2), 2, '.', '');
        } else {
            // Inference based on payment type / credit_mode
            if (!$hasExplicitPaymentType && strtolower((string) ($rawCreditMode ?? '')) === 'semi') {
                // Legacy semi: infer from customer credit balance if available
                $creditBalance = 1000.00;
                if ($customerId) {
                    $creditRecord = CustomerCredit::where('customer_id', $customerId)->first();
                    $creditBalance = $creditRecord ? (float) $creditRecord->balance : 1000.00;
                }

                $totalFloat = (float) $totalAmount;
                if ($totalFloat <= $creditBalance) {
                    $creditAmount = $totalAmount;
                    $razorpayAmount = '0.00';
                    $normalizedPaymentType = 'FULL_CREDIT';
                } else {
                    $creditAmount = number_format($creditBalance, 2, '.', '');
                    $razorpayAmount = number_format($totalFloat - $creditBalance, 2, '.', '');
                    $normalizedPaymentType = 'SEMI';
                }
            } else {
                $razorpayAmount = match ($normalizedPaymentType) {
                    'FULL_CREDIT' => '0.00',
                    default => $totalAmount,
                };

                $creditAmount = match ($normalizedPaymentType) {
                    'FULL_CREDIT' => $totalAmount,
                    default => '0.00',
                };
            }
        }

        $razorpayOrderId = $data['razorpay_order_id'] ?? null;
        $razorpayPaymentId = $data['razorpay_payment_id'] ?? null;
        $paymentStatus = strtoupper($data['payment_status'] ?? 'COMPLETED');
        $transactionId = $data['transaction_id'] ?? $razorpayPaymentId ?? $razorpayOrderId ?? ('TXN_' . uniqid('', true));

        return new self(
            paymentType: $normalizedPaymentType,
            totalAmount: $totalAmount,
            razorpayAmount: $razorpayAmount,
            creditAmount: $creditAmount,
            razorpayOrderId: $razorpayOrderId,
            razorpayPaymentId: $razorpayPaymentId,
            paymentStatus: $paymentStatus,
            transactionId: $transactionId,
            itemType: $itemType,
            jobOrOfferId: $jobOrOfferId,
            customerId: $customerId,
            rawCreditMode: $rawCreditMode ? strtolower((string) $rawCreditMode) : null,
            mobile: $mobile ? (string) $mobile : null,
        );
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'payment_type' => $this->paymentType,
            'total_amount' => $this->totalAmount,
            'razorpay_amount' => $this->razorpayAmount,
            'credit_amount' => $this->creditAmount,
            'razorpay_order_id' => $this->razorpayOrderId,
            'razorpay_payment_id' => $this->razorpayPaymentId,
            'payment_status' => $this->paymentStatus,
            'transaction_id' => $this->transactionId,
            'item_type' => $this->itemType,
            'job_or_offer_id' => $this->jobOrOfferId,
            'credit_mode' => $this->rawCreditMode ?? strtolower($this->paymentType),
            'amount' => $this->totalAmount,
        ];
    }
}
