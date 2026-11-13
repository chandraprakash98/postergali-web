<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateInvoiceRequest;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    /**
     * Generate and stream/open Razorpay or Apple In-App Purchase Credit Top-up Tax Invoice.
     * Supports direct open (stream inline) or forced download via ?download=1.
     *
     * @route GET/POST /api/v1/credits/invoice
     * @route GET/POST /api/v1/payments/transaction-invoice
     */
    public function creditInvoice(GenerateInvoiceRequest $request): Response|JsonResponse|SymfonyResponse
    {
        try {
            $mobile = $request->normalizedMobile();
            $customerId = $request->resolvedCustomerId();
            $transactionId = $request->resolvedTransactionId();

            Log::info('[InvoiceController][CreditInvoice] Generating credit top-up tax invoice', [
                'mobile' => $mobile,
                'customer_id' => $customerId,
                'transaction_id' => $transactionId,
                'credit_ledger_id' => $request->input('credit_ledger_id'),
                'wants_download' => $request->wantsDownload(),
            ]);

            $result = $request->filled('credit_ledger_id')
                ? $this->invoiceService->generateCreditLedgerInvoicePdf(
                    (string) $mobile,
                    (int) $request->input('credit_ledger_id')
                )
                : $this->invoiceService->generateCreditTransactionInvoicePdf(
                    $mobile,
                    $customerId,
                    $transactionId
                );

            // Direct open in browser viewer by default; force download if requested
            if ($request->wantsDownload()) {
                return $result['pdf']->download($result['filename']);
            }

            return $result['pdf']->stream($result['filename']);

        } catch (NotFoundHttpException $e) {
            Log::warning('[InvoiceController][CreditInvoice] Invoice generation 404', [
                'message' => $e->getMessage(),
                'mobile' => $request->input('mobile') ?? $request->input('mobile_number'),
                'customer_id' => $request->resolvedCustomerId(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (Throwable $e) {
            Log::error('[InvoiceController][CreditInvoice] Credit invoice generation failed', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate credit invoice PDF. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Download or stream customer payments invoice as PDF directly without server storage.
     *
     * @route GET/POST /api/v1/customers/invoice
     * @route GET/POST /api/v1/payments/invoice
     */
    public function downloadInvoice(GenerateInvoiceRequest $request): Response|JsonResponse|SymfonyResponse
    {
        try {
            $mobile = $request->normalizedMobile();
            $customerId = $request->resolvedCustomerId();

            // If user explicitly asks for credit top-up invoice or passes a transaction ID, forward to creditInvoice
            if ($request->input('type') === 'credit' || $request->input('type') === 'topup' || $request->has('transaction_uuid') || $request->has('transaction_id')) {
                return $this->creditInvoice($request);
            }

            try {
                $result = $this->invoiceService->generateCustomerInvoicePdf($mobile, $customerId);

                // If stream parameter or direct open requested
                if ($request->has('stream') || $request->input('view') === 'inline') {
                    return $result['pdf']->stream($result['filename']);
                }

                return $result['pdf']->download($result['filename']);

            } catch (NotFoundHttpException $e) {
                // If no posting payments exist, check if customer has credit top-ups and offer that invoice
                try {
                    $creditResult = $this->invoiceService->generateCreditTransactionInvoicePdf($mobile, $customerId);
                    if ($request->has('stream') || $request->input('view') === 'inline' || !$request->wantsDownload()) {
                        return $creditResult['pdf']->stream($creditResult['filename']);
                    }
                    return $creditResult['pdf']->download($creditResult['filename']);
                } catch (\Throwable) {
                    throw $e; // Re-throw original 404
                }
            }

        } catch (NotFoundHttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (Throwable $e) {
            Log::error('Invoice generation failed: ' . $e->getMessage(), [
                'mobile' => $request->input('mobile'),
                'customer_id' => $request->resolvedCustomerId(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice PDF. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
