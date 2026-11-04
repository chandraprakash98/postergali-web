<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $invoice_number }}</title>
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
            font-size: 9.5px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .company-info {
            font-size: 9.5px;
            color: #475569;
            line-height: 1.4;
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
            line-height: 1.45;
        }
        .invoice-meta strong {
            color: #0f172a;
        }
        .details-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-container td {
            vertical-align: top;
            width: 50%;
        }
        .details-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-right: 6px;
        }
        .details-box-right {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-left: 6px;
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
            width: 100px;
        }
        .info-value {
            color: #0f172a;
            font-weight: 600;
        }
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .badge-razorpay {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .badge-apple {
            background-color: #f1f5f9;
            color: #0f172a;
            border: 1px solid #cbd5e1;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .items-table th {
            background-color: #4f46e5;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 8px;
            text-align: left;
            border: 1px solid #4338ca;
        }
        .items-table th.text-right,
        .items-table td.text-right {
            text-align: right;
        }
        .items-table th.text-center,
        .items-table td.text-center {
            text-align: center;
        }
        .items-table td {
            padding: 8px 8px;
            font-size: 9.5px;
            border: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .summary-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }
        .summary-container td {
            vertical-align: top;
        }
        .summary-box {
            width: 48%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 5px 8px;
            font-size: 9.5px;
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
            background-color: #f8fafc;
            border-left: 3px solid #4f46e5;
            border-radius: 4px;
            font-size: 9px;
            color: #475569;
            margin-bottom: 22px;
            line-height: 1.4;
        }
        .notes-title {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-size: 9px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .signature-table td {
            vertical-align: bottom;
        }
        .auth-sign-box {
            text-align: right;
            font-size: 9.5px;
            color: #334155;
        }
        .auth-stamp {
            display: inline-block;
            border: 2px solid #4f46e5;
            color: #4f46e5;
            font-weight: 800;
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            line-height: 1.35;
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
                <div class="invoice-badge-title">TAX INVOICE</div>
                <div class="invoice-meta">
                    <strong>Invoice #:</strong> {{ $invoice_number }}<br/>
                    <strong>Issue Date:</strong> {{ $issue_date }}<br/>
                    <strong>Paid At:</strong> {{ $paid_at }}<br/>
                    <strong>Status:</strong> <span class="badge badge-success">PAID</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Customer & Payment Details -->
    <table class="details-container">
        <tr>
            <td>
                <div class="details-box">
                    <div class="box-title">Billed To (Customer Details)</div>
                    <div class="info-row">
                        <span class="info-label">PosterGali ID:</span>
                        <span class="info-value">{{ $customer->customer_id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Mobile Number:</span>
                        <span class="info-value">{{ $customer->mobile }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Wallet Balance:</span>
                        <span class="info-value">{{ number_format($credit_balance, 2) }} Credits</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Place of Supply:</span>
                        <span class="info-value">Uttar Pradesh (09)</span>
                    </div>
                </div>
            </td>
            <td>
                <div class="details-box-right">
                    <div class="box-title">Payment & Gateway Verification</div>
                    <div class="info-row">
                        <span class="info-label">Provider:</span>
                        <span class="info-value">
                            @if(strtoupper($transaction->payment_provider) === 'RAZORPAY')
                                <span class="badge badge-razorpay">RAZORPAY</span>
                            @else
                                <span class="badge badge-apple">APPLE IN-APP PURCHASE</span>
                            @endif
                        </span>
                    </div>
                    @if(!empty($transaction->provider_order_id))
                    <div class="info-row">
                        <span class="info-label">Order ID:</span>
                        <span class="info-value">{{ $transaction->provider_order_id }}</span>
                    </div>
                    @endif
                    @if(!empty($transaction->provider_payment_id))
                    <div class="info-row">
                        <span class="info-label">Payment ID:</span>
                        <span class="info-value">{{ $transaction->provider_payment_id }}</span>
                    </div>
                    @endif
                    @if(!empty($transaction->provider_transaction_id))
                    <div class="info-row">
                        <span class="info-label">Apple Txn ID:</span>
                        <span class="info-value">{{ $transaction->provider_transaction_id }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Verification:</span>
                        <span class="info-value"><span class="badge badge-success">VERIFIED & SETTLED</span></span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 45%;">Item Description</th>
                <th style="width: 12%;" class="text-center">HSN/SAC</th>
                <th style="width: 13%;" class="text-center">Credits</th>
                <th style="width: 12%;" class="text-right">Rate</th>
                <th style="width: 13%;" class="text-right">Amount (INR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>
                    <strong>PosterGali Consumable Credit Pack</strong><br/>
                    <span style="font-size: 8.5px; color: #64748b;">
                        Package: {{ $pack_title }} &nbsp;|&nbsp; Code: {{ $pack_code }}
                        @if(!empty($transaction->provider_product_id))
                            &nbsp;|&nbsp; Product: {{ $transaction->provider_product_id }}
                        @endif
                    </span>
                </td>
                <td class="text-center">998439</td>
                <td class="text-center"><strong>{{ number_format($credits_granted) }}</strong></td>
                <td class="text-right">{{ number_format($taxable_amount, 2) }}</td>
                <td class="text-right"><strong>{{ number_format($taxable_amount, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Financial Summary -->
    <table class="summary-container">
        <tr>
            <td style="width: 50%; padding-right: 15px;">
                <div class="notes-box">
                    <div class="notes-title">Tax & Digital Compliance Summary</div>
                    • All consumable credits are immediately added to the customer wallet and are available for poster, job, and offer promotions on PosterGali.<br/>
                    • GST applied under SAC 998439 (Digital and Online Advertising Services).<br/>
                    • Transaction UUID: {{ $transaction->transaction_uuid }}
                </div>
            </td>
            <td style="width: 50%;">
                <table class="summary-box">
                    <tr>
                        <td class="label">Taxable Value:</td>
                        <td class="value">INR {{ number_format($taxable_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">CGST (9.0%):</td>
                        <td class="value">INR {{ number_format($cgst, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">SGST (9.0%):</td>
                        <td class="value">INR {{ number_format($sgst, 2) }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td class="label">TOTAL PAID (INR):</td>
                        <td class="value">INR {{ number_format($total_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Signatory Section -->
    <table class="signature-table">
        <tr>
            <td style="width: 60%; font-size: 9px; color: #64748b;">
                <strong>Payment Mode:</strong> {{ strtoupper($transaction->payment_provider) }} Verified Payment<br/>
                <strong>Currency:</strong> {{ $transaction->currency ?? 'INR' }} (Indian Rupee)<br/>
                <em>This invoice was generated automatically upon payment settlement.</em>
            </td>
            <td style="width: 40%;">
                <div class="auth-sign-box">
                    <div class="auth-stamp">VERIFIED BY POSTERGALI</div><br/>
                    <strong>Authorized Signatory</strong><br/>
                    <span style="font-size: 8.5px; color: #64748b;">PosterGali Accounts & Finance</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p><strong>PosterGali Services Private Limited</strong> &bull; Sector 62, Noida, Uttar Pradesh, 201309 &bull; GSTIN: 07AABCP1234F1Z5</p>
        <p>Thank you for choosing PosterGali. For invoice queries, please write to support@postergali.com quoting Invoice # {{ $invoice_number }}.</p>
        <p>This is an electronically generated document and does not require a physical signature.</p>
    </div>

</body>
</html>

