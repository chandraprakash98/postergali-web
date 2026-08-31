<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $customer->customer_id }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.45;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
        }
        .header-table td {
            vertical-align: top;
        }
        .brand-title {
            font-size: 22px;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: -0.5px;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .brand-subtitle {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
        }
        .company-info {
            font-size: 9.5px;
            color: #475569;
            line-height: 1.35;
        }
        .invoice-badge-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            text-align: right;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
        }
        .invoice-meta {
            text-align: right;
            font-size: 9.5px;
            color: #334155;
        }
        .invoice-meta strong {
            color: #0f172a;
        }
        .details-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }
        .details-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            vertical-align: top;
        }
        .box-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #4f46e5;
            margin-bottom: 6px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 4px;
        }
        .info-row {
            margin-bottom: 3px;
            font-size: 9.5px;
        }
        .info-label {
            color: #64748b;
            display: inline-block;
            width: 90px;
        }
        .info-value {
            color: #0f172a;
            font-weight: 600;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-primary {
            background-color: #e0e7ff;
            color: #3730a3;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-secondary {
            background-color: #f1f5f9;
            color: #475569;
        }
        .table-heading {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .payments-table th {
            background-color: #4f46e5;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #4338ca;
        }
        .payments-table th.text-right,
        .payments-table td.text-right {
            text-align: right;
        }
        .payments-table th.text-center,
        .payments-table td.text-center {
            text-align: center;
        }
        .payments-table td {
            padding: 7px 6px;
            font-size: 9.5px;
            border: 1px solid #e2e8f0;
            color: #334155;
        }
        .payments-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .summary-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .summary-container td {
            vertical-align: top;
        }
        .summary-box {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 5px 8px;
            font-size: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary-box .label {
            color: #64748b;
            font-weight: 500;
        }
        .summary-box .value {
            text-align: right;
            color: #0f172a;
            font-weight: 600;
        }
        .summary-box tr.grand-total td {
            background-color: #4f46e5;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            border: none;
            padding: 7px 8px;
        }
        .summary-box tr.grand-total .label,
        .summary-box tr.grand-total .value {
            color: #ffffff;
        }
        .notes-box {
            padding: 8px 12px;
            background-color: #f1f5f9;
            border-left: 3px solid #4f46e5;
            border-radius: 4px;
            font-size: 9px;
            color: #475569;
            margin-bottom: 25px;
        }
        .notes-title {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 3px;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
        }
        .footer p {
            margin: 2px 0;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="brand-title">{{ $company['name'] }}</div>
                <div class="brand-subtitle">{{ $company['tagline'] }}</div>
                <div class="company-info">
                    {{ $company['address'] }}<br/>
                    <strong>Email:</strong> {{ $company['support_email'] }} &nbsp;|&nbsp; <strong>Phone:</strong> {{ $company['support_phone'] }}<br/>
                    <strong>GSTIN:</strong> {{ $company['gstin'] }} &nbsp;|&nbsp; <strong>Web:</strong> {{ $company['website'] }}
                </div>
            </td>
            <td style="width: 45%;">
                <div class="invoice-badge-title">PAYMENT INVOICE</div>
                <div class="invoice-meta">
                    <strong>Invoice #:</strong> {{ $invoice_number }}<br/>
                    <strong>Issue Date:</strong> {{ $issue_date }}<br/>
                    <strong>Generated At:</strong> {{ $generated_at }}<br/>
                    <strong>Status:</strong> <span class="badge badge-success">COMPLETED</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Customer Details & Account Summary -->
    <table class="details-container">
        <tr>
            <td style="width: 48%;">
                <div class="details-box">
                    <div class="box-title">Billed To (Customer Details)</div>
                    <div class="info-row">
                        <span class="info-label">Customer ID:</span>
                        <span class="info-value">{{ $customer->customer_id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Mobile Number:</span>
                        <span class="info-value">{{ $customer->mobile }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Customer Since:</span>
                        <span class="info-value">{{ $customer->created_at ? $customer->created_at->format('d M Y') : 'N/A' }}</span>
                    </div>
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%;">
                <div class="details-box">
                    <div class="box-title">Wallet & Account Summary</div>
                    <div class="info-row">
                        <span class="info-label">Credit Balance:</span>
                        <span class="info-value" style="color: #059669;">&#8377; {{ number_format($credit_balance, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Payments:</span>
                        <span class="info-value">{{ $totals['count'] }} Transaction{{ $totals['count'] > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Amount:</span>
                        <span class="info-value">&#8377; {{ number_format((float) $totals['total_amount'], 2) }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Payments Records Table -->
    <div class="table-heading">Transaction & Payment Breakdown</div>
    <table class="payments-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 14%;">Date & Time</th>
                <th style="width: 18%;">Transaction ID</th>
                <th style="width: 14%;">Item / Purpose</th>
                <th style="width: 11%;">Type</th>
                <th style="width: 12%;" class="text-right">UPI Paid (&#8377;)</th>
                <th style="width: 12%;" class="text-right">Credit Used (&#8377;)</th>
                <th style="width: 14%;" class="text-right">Total (&#8377;)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $index => $payment)
                @php
                    $itemLabel = 'Payment';
                    if ($payment->item_type === 'job' || $payment->job) {
                        $jobTitle = $payment->job?->job_role ?? 'Job Ad';
                        $itemLabel = 'Job: ' . $jobTitle;
                    } elseif ($payment->item_type === 'offer' || $payment->offer) {
                        $offerTitle = $payment->offer?->business_name ?? 'Offer Ad';
                        $itemLabel = 'Offer: ' . $offerTitle;
                    } elseif (!empty($payment->item_type)) {
                        $itemLabel = ucfirst((string) $payment->item_type);
                    }

                    $totalItemAmount = (float) ($payment->total_amount ?? $payment->amount ?? 0);
                    $razorpayAmt = (float) ($payment->razorpay_amount ?? 0);
                    $creditAmt = (float) ($payment->credit_amount ?? 0);
                    $payType = strtoupper($payment->payment_type ?? $payment->credit_mode ?? 'FULL_UPI');
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $payment->created_at ? $payment->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                    <td style="font-family: monospace; font-size: 8.5px;">{{ $payment->transaction_id ?? 'N/A' }}</td>
                    <td>
                        <strong>{{ $itemLabel }}</strong>
                        @if($payment->razorpay_payment_id)
                            <br/><span style="font-size: 8px; color: #64748b;">RP: {{ $payment->razorpay_payment_id }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $payType === 'FULL_CREDIT' ? 'badge-primary' : ($payType === 'SEMI' ? 'badge-warning' : 'badge-secondary') }}">
                            {{ str_replace('_', ' ', $payType) }}
                        </span>
                    </td>
                    <td class="text-right">&#8377; {{ number_format($razorpayAmt, 2) }}</td>
                    <td class="text-right">&#8377; {{ number_format($creditAmt, 2) }}</td>
                    <td class="text-right" style="font-weight: 700;">&#8377; {{ number_format($totalItemAmount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Summary Table -->
    <table class="summary-container">
        <tr>
            <td style="width: 50%;">
                <div class="notes-box">
                    <div class="notes-title">Payment & Wallet Notes</div>
                    This is a computer-generated summary invoice showing all registered transactions associated with Customer ID <strong>{{ $customer->customer_id }}</strong>. Credits redeemed have been automatically adjusted from your PosterGali wallet balance.
                </div>
            </td>
            <td style="width: 50%;">
                <table class="summary-box">
                    <tr>
                        <td class="label">Total UPI / Gateway Paid:</td>
                        <td class="value">&#8377; {{ number_format((float) $totals['total_razorpay'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Total Credits Redeemed:</td>
                        <td class="value">&#8377; {{ number_format((float) $totals['total_credit'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Total Transactions:</td>
                        <td class="value">{{ $totals['count'] }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td class="label">GRAND TOTAL:</td>
                        <td class="value">&#8377; {{ number_format((float) $totals['total_amount'], 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer Section -->
    <div class="footer">
        <p>Thank you for choosing <strong>{{ $company['name'] }}</strong> for your business and hiring needs.</p>
        <p>For any billing inquiries, please contact our support team at {{ $company['support_email'] }} or call {{ $company['support_phone'] }}.</p>
        <p><em>PosterGali Technologies &copy; {{ date('Y') }} &bull; All Rights Reserved.</em></p>
    </div>

</body>
</html>
