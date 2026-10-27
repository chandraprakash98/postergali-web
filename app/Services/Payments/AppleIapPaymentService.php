<?php

namespace App\Services\Payments;

use App\Models\CreditPack;
use App\Models\Plan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AppleIapPaymentService
{
    protected string $bundleId;
    protected string $environment;

    public function __construct()
    {
        $this->bundleId = (string) config('services.apple.bundle_id', 'com.postergali.postergali');
        $this->environment = (string) config('services.apple.environment', 'Sandbox');
    }

    /**
     * Verify an Apple IAP transaction token / JWS or receipt.
     *
     * @return array{
     *     transaction_id: string,
     *     original_transaction_id: ?string,
     *     product_id: string,
     *     purchase_date: int,
     *     environment: string,
     *     is_revoked: bool
     * }
     */
    public function verifyTransaction(string $transactionData, CreditPack|Plan $expectedPlan): array
    {
        $trimmedData = trim($transactionData);
        $sourceFormat = substr_count($trimmedData, '.') === 2
            ? 'jws'
            : (is_array(json_decode($trimmedData, true)) ? 'json' : 'transaction_id');

        Log::info('Apple IAP transaction verification started', [
            'expected_product_id' => $expectedPlan->product_id,
            'source_format' => $sourceFormat,
            'configured_environment' => $this->environment,
        ]);

        $decoded = $this->decodeTransactionPayload($transactionData);
        $verificationContext = [
            'transaction_id' => $decoded['transaction_id'] ?? null,
            'product_id' => $decoded['product_id'] ?? null,
            'environment' => $decoded['environment'] ?? null,
        ];

        // 1. Verify product_id matches the plan
        $productId = $decoded['product_id'] ?? null;
        if ($productId !== $expectedPlan->product_id) {
            Log::warning('Apple IAP transaction verification rejected', $verificationContext + [
                'reason' => 'product_mismatch',
                'expected_product_id' => $expectedPlan->product_id,
            ]);
            throw new RuntimeException("Apple IAP product mismatch: expected '{$expectedPlan->product_id}', got '{$productId}'.");
        }

        // 2. Verify transaction is not revoked/refunded
        if (!empty($decoded['is_revoked'])) {
            Log::warning('Apple IAP transaction verification rejected', $verificationContext + [
                'reason' => 'transaction_revoked',
            ]);
            throw new RuntimeException("Apple IAP transaction has been revoked or refunded.");
        }

        // 3. Verify transaction id exists
        if (empty($decoded['transaction_id'])) {
            Log::warning('Apple IAP transaction verification rejected', $verificationContext + [
                'reason' => 'transaction_id_missing',
            ]);
            throw new RuntimeException("Apple IAP transaction ID is missing from verification payload.");
        }

        Log::info('Apple IAP transaction verification accepted', $verificationContext + [
            'original_transaction_id' => $decoded['original_transaction_id'] ?? null,
            'purchase_date' => $decoded['purchase_date'] ?? null,
            'is_revoked' => false,
        ]);

        return $decoded;
    }

    /**
     * Decode either a JWS signed transaction token, a JSON string, or a direct transaction identifier.
     */
    public function decodeTransactionPayload(string $data): array
    {
        $trimmed = trim($data);

        // Check if it's a JWS token (header.payload.signature)
        if (substr_count($trimmed, '.') === 2) {
            $parts = explode('.', $trimmed);
            $payloadJson = $this->base64UrlDecode($parts[1]);
            $payload = json_decode($payloadJson, true);

            if (is_array($payload)) {
                return [
                    'transaction_id' => (string) ($payload['transactionId'] ?? $payload['transaction_id'] ?? ''),
                    'original_transaction_id' => (string) ($payload['originalTransactionId'] ?? $payload['original_transaction_id'] ?? ''),
                    'product_id' => (string) ($payload['productId'] ?? $payload['product_id'] ?? ''),
                    'purchase_date' => (int) ($payload['purchaseDate'] ?? $payload['purchase_date'] ?? time()),
                    'environment' => (string) ($payload['environment'] ?? $this->environment),
                    'is_revoked' => !empty($payload['revocationDate']) || !empty($payload['revocation_date']),
                ];
            }
        }

        // Check if it's JSON
        $json = json_decode($trimmed, true);
        if (is_array($json)) {
            return [
                'transaction_id' => (string) ($json['transactionId'] ?? $json['transaction_id'] ?? ''),
                'original_transaction_id' => (string) ($json['originalTransactionId'] ?? $json['original_transaction_id'] ?? null),
                'product_id' => (string) ($json['productId'] ?? $json['product_id'] ?? ''),
                'purchase_date' => (int) ($json['purchaseDate'] ?? $json['purchase_date'] ?? time()),
                'environment' => (string) ($json['environment'] ?? $this->environment),
                'is_revoked' => !empty($json['revocationDate']) || !empty($json['revocation_date']),
            ];
        }

        // Fallback: If it's a raw transaction ID string, provide standard parsed structure
        return [
            'transaction_id' => $trimmed,
            'original_transaction_id' => $trimmed,
            'product_id' => '', // Will fail if product_id cannot be matched, unless resolved via App Store API
            'purchase_date' => time(),
            'environment' => $this->environment,
            'is_revoked' => false,
        ];
    }

    /**
     * Decode App Store Server Notification V2 payload.
     */
    public function decodeNotificationPayload(array|string $payload): array
    {
        if (is_string($payload)) {
            $payload = json_decode($payload, true) ?? [];
        }

        $signedPayload = $payload['signedPayload'] ?? null;
        if ($signedPayload && substr_count($signedPayload, '.') === 2) {
            $parts = explode('.', $signedPayload);
            $decoded = json_decode($this->base64UrlDecode($parts[1]), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $payload;
    }

    private function base64UrlDecode(string $input): string
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }

        return (string) base64_decode(strtr($input, '-_', '+/'));
    }
}
