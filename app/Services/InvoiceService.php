<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\PaymentTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDFInstance;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceService
{
    /**
     * Resolve Customer by PosterGali ID (customer_id) or Mobile number.
     *
     * @throws NotFoundHttpException
     */
    public function resolveCustomer(?string $mobile = null, ?string $customerId = null): Customer
    {
        if (!empty($customerId)) {
            $customer = Customer::where('customer_id', trim($customerId))->first();
            if ($customer) {
                return $customer;
            }
        }

        if (!empty($mobile)) {
            $digits = preg_replace('/[^0-9]/', '', $mobile);
            $candidates = [$digits];

            if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
                $candidates[] = substr($digits, 2);
            }
            if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
                $candidates[] = substr($digits, 1);
            }
            if (strlen($digits) === 10) {
                $candidates[] = '91' . $digits;
                $candidates[] = '0' . $digits;
            }

            $customer = Customer::whereIn('mobile', array_values(array_unique($candidates)))->first();
            if ($customer) {
                return $customer;
            }
        }

        if (!empty($mobile) && empty($customerId)) {
            throw new NotFoundHttpException('Customer not found for the provided mobile number.');
        }

        if (empty($mobile) && !empty($customerId)) {
            throw new NotFoundHttpException('Customer not found for the provided PosterGali ID.');
        }

        throw new NotFoundHttpException('Customer not found for the provided mobile number or PosterGali ID.');
    }

    /**
     * Find a specific or latest successful Razorpay or Apple IAP PaymentTransaction for a customer.
     *
     * @throws NotFoundHttpException
     */
    public function findPaymentTransaction(Customer $customer, ?string $transactionId = null): PaymentTransaction
    {
        $query = PaymentTransaction::where('customer_id', $customer->customer_id)
            ->where('status', PaymentTransaction::STATUS_SUCCESS)
            ->with(['creditPack', 'customer']);

        if (!empty($transactionId)) {
            $query->where(function ($q) use ($transactionId) {
                $q->where('transaction_uuid', $transactionId)
                  ->orWhere('provider_transaction_id', $transactionId)
                  ->orWhere('provider_order_id', $transactionId)
                  ->orWhere('provider_payment_id', $transactionId);
            });
        } else {
            $query->orderByDesc('paid_at')->orderByDesc('id');
        }

        $transaction = $query->first();

        if (!$transaction) {
            throw new NotFoundHttpException('No successful Razorpay or Apple IAP payment transactions found for this customer.');
        }

        return $transaction;
    }

    /**
     * Generate Production-Ready PDF Tax Invoice for a Razorpay or Apple IAP credit top-up.
     *
     * @throws NotFoundHttpException
     */
    public function generateCreditTransactionInvoicePdf(
        ?string $mobile = null,
        ?string $customerId = null,
        ?string $transactionId = null
    ): array {
        $customer = $this->resolveCustomer($mobile, $customerId);
        $transaction = $this->findPaymentTransaction($customer, $transactionId);

        $credit = CustomerCredit::where('customer_id', $customer->customer_id)->first();
        $creditBalance = $credit ? (float) $credit->balance : 0.00;

        $amount = (float) $transaction->amount;
        // Standard digital services GST in India (18% inclusive)
        $taxableAmount = round($amount / 1.18, 2);
        $totalTax = round($amount - $taxableAmount, 2);
        $cgst = round($totalTax / 2, 2);
        $sgst = round($totalTax - $cgst, 2);

        $paidDate = $transaction->paid_at ?? $transaction->created_at ?? now();
        $invoiceNumber = 'INV-' . ($transaction->payment_provider === PaymentTransaction::PROVIDER_APPLE_IAP ? 'IAP' : 'RZP')
            . '-' . $paidDate->format('Ymd') . '-' . strtoupper(substr(str_replace('-', '', $transaction->transaction_uuid), 0, 6));

        $packTitle = $transaction->creditPack?->name ?? $transaction->creditPack?->plan_title ?? 'Credit Pack';
        $packCode = $transaction->creditPack?->pack_code ?? $transaction->creditPack?->plan_code ?? 'credits';

        $invoiceData = [
            'invoice_number' => $invoiceNumber,
            'issue_date' => $paidDate->format('d M Y'),
            'paid_at' => $paidDate->format('d M Y, h:i A'),
            'customer' => $customer,
            'credit_balance' => $creditBalance,
            'transaction' => $transaction,
            'pack_title' => $packTitle,
            'pack_code' => $packCode,
            'credits_granted' => $transaction->credits_to_add,
            'taxable_amount' => $taxableAmount,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'total_amount' => $amount,
            'company' => [
                'name' => config('app.name', 'PosterGali'),
                'tagline' => 'Connecting Local Businesses & Opportunities',
                'support_email' => 'support@postergali.com',
                'support_phone' => '+91 98765 43210',
                'website' => url('/'),
                'address' => 'PosterGali Services Private Limited, Sector 62, Noida, Uttar Pradesh, 201309',
                'gstin' => '07AABCP1234F1Z5',
            ],
        ];

        /** @var DomPDFInstance $pdf */
        $pdf = Pdf::loadView('invoices.credit_transaction_invoice', $invoiceData);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'invoice_' . $customer->customer_id . '_' . strtolower($transaction->payment_provider) . '_' . $paidDate->format('Ymd_His') . '.pdf';

        return [
            'pdf' => $pdf,
            'filename' => $filename,
            'customer' => $customer,
            'transaction' => $transaction,
        ];
    }

    /**
     * Fetch customer, verify payments, build invoice payload, and render PDF dynamically (for posting checkout payments).
     *
     * @throws NotFoundHttpException
     */
    public function generateCustomerInvoicePdf(?string $mobile = null, ?string $customerId = null): array
    {
        $customer = $this->resolveCustomer($mobile, $customerId);

        /** @var Collection $payments */
        $payments = $customer->payments()
            ->with(['job', 'offer'])
            ->orderByDesc('created_at')
            ->get();

        if ($payments->isEmpty()) {
            throw new NotFoundHttpException('No payment records found for this customer.');
        }

        $credit = CustomerCredit::where('customer_id', $customer->customer_id)->first();
        $creditBalance = $credit ? (float) $credit->balance : 0.00;

        $totalAmount = 0.00;
        $totalRazorpay = 0.00;
        $totalCredit = 0.00;

        foreach ($payments as $payment) {
            $totalAmount = (float) bcadd(
                number_format($totalAmount, 2, '.', ''),
                number_format((float) ($payment->total_amount ?? $payment->amount ?? 0), 2, '.', ''),
                2
            );
            $totalRazorpay = (float) bcadd(
                number_format($totalRazorpay, 2, '.', ''),
                number_format((float) ($payment->razorpay_amount ?? 0), 2, '.', ''),
                2
            );
            $totalCredit = (float) bcadd(
                number_format($totalCredit, 2, '.', ''),
                number_format((float) ($payment->credit_amount ?? 0), 2, '.', ''),
                2
            );
        }

        $invoiceData = [
            'invoice_number' => 'INV-' . $customer->customer_id . '-' . date('YmdHis'),
            'generated_at' => now()->format('d M Y, h:i A'),
            'issue_date' => now()->format('d M Y'),
            'customer' => $customer,
            'credit_balance' => $creditBalance,
            'payments' => $payments,
            'totals' => [
                'total_amount' => number_format($totalAmount, 2, '.', ''),
                'total_razorpay' => number_format($totalRazorpay, 2, '.', ''),
                'total_credit' => number_format($totalCredit, 2, '.', ''),
                'count' => $payments->count(),
            ],
            'company' => [
                'name' => config('app.name', 'PosterGali'),
                'tagline' => 'Connecting Local Businesses & Opportunities',
                'support_email' => 'support@postergali.com',
                'support_phone' => '+91 98765 43210',
                'website' => url('/'),
                'address' => 'PosterGali Services, Sector 62, Noida, Uttar Pradesh, 201309',
                'gstin' => '07AABCP1234F1Z5',
            ],
        ];

        /** @var DomPDFInstance $pdf */
        $pdf = Pdf::loadView('invoices.customer_invoice', $invoiceData);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'invoice_' . $customer->customer_id . '_' . date('Ymd_His') . '.pdf';

        return [
            'pdf' => $pdf,
            'filename' => $filename,
            'customer' => $customer,
            'total_payments' => $payments->count(),
        ];
    }
}
