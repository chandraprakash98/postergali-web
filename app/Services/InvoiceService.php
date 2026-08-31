<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerCredit;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDFInstance;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceService
{
    /**
     * Fetch customer, verify payments, build invoice payload, and render PDF dynamically.
     *
     * @throws NotFoundHttpException
     */
    public function generateCustomerInvoicePdf(string $mobile): array
    {
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
        if (!$customer) {
            throw new NotFoundHttpException('Customer not found for the provided mobile number.');
        }

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
