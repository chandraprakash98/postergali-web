<?php

namespace App\Services;

use App\Models\OtpVerification;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OtpService
{
    public function send(string $mobile): OtpVerification
    {
        $code = $this->generateCode();
        $expiresAt = now()->addMinutes((int) config('otp.expiry_minutes', 5));

        $message = "Postergali - Use OTP {$code} to complete your verification. Please do not share this OTP with anyone.\nUNITYGRID PVT LTD";
        $requestData = [
            'uname' => config('services.bulk_sms.username'),
            'pass' => config('services.bulk_sms.password'),
            'send' => config('services.bulk_sms.sender'),
            'dest' => $mobile,
            'msg' => $message,
        ];

        Log::info('OTP provider request started.', [
            'url' => config('services.bulk_sms.url'),
            'username' => $requestData['uname'],
            'sender' => $requestData['send'],
            'destination' => $requestData['dest'],
            'message' => preg_replace('/\b\d{6}\b/', '[REDACTED]', $message),
        ]);

        $response = $this->client()->get(config('services.bulk_sms.url'), $requestData);
        $responseBody = $response->json();

        Log::info('OTP provider response received.', [
            'status' => $response->status(),
            'body' => is_array($responseBody) ? $responseBody : $response->body(),
        ]);

        if ($response->failed() || $response->json('isSuccess') !== true) {
            throw new RuntimeException('OTP provider rejected the message: ' . ($response->json('returnMessage') ?: 'unknown provider response'));
        }

        return OtpVerification::create([
            'mobile' => $mobile,
            'code_hash' => Hash::make($code),
            'expires_at' => $expiresAt,
            'provider_token' => $response->json('data'),
        ]);
    }

    public function verify(string $mobile, string $code): bool
    {
        return DB::transaction(function () use ($mobile, $code): bool {
            $verification = OtpVerification::where('mobile', $mobile)
                ->whereNull('verified_at')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (!$verification || $verification->expires_at->isPast() || $verification->attempts >= (int) config('otp.max_attempts', 5)) {
                return false;
            }

            $verification->increment('attempts');

            if (!Hash::check($code, $verification->code_hash)) {
                return false;
            }

            $verification->forceFill(['verified_at' => now()])->save();

            return true;
        });
    }

    protected function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    protected function client(): PendingRequest
    {
        return Http::acceptJson()
            ->connectTimeout((int) config('services.bulk_sms.timeout', 10))
            ->timeout((int) config('services.bulk_sms.timeout', 10));
    }
}