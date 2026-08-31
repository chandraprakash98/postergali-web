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
     * Download or stream customer payments invoice as PDF directly without server storage.
     */
    public function downloadInvoice(GenerateInvoiceRequest $request): Response|JsonResponse|SymfonyResponse
    {
        try {
            $mobile = $request->normalizedMobile();

            $result = $this->invoiceService->generateCustomerInvoicePdf($mobile);

            return $result['pdf']->download($result['filename']);
        } catch (NotFoundHttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (Throwable $e) {
            Log::error('Invoice generation failed: ' . $e->getMessage(), [
                'mobile' => $request->input('mobile'),
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
