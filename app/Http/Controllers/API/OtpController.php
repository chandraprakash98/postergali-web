<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class OtpController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    public function send(Request $request)
    {
        $mobile = $this->mobile($request);

        try {
            $this->otpService->send($mobile);
        } catch (RuntimeException $exception) {
            Log::error('OTP provider request failed.', ['mobile' => $mobile, 'error' => $exception->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Unable to send OTP right now.'], 502);
        }

        return response()->json(['success' => true, 'message' => 'OTP sent successfully.']);
    }

    public function verify(Request $request)
    {
        $mobile = $this->mobile($request);
        $validated = $request->validate(['otp' => ['required', 'digits:6']]);

        if (!$this->otpService->verify($mobile, $validated['otp'])) {
            throw ValidationException::withMessages(['otp' => ['The OTP is invalid or expired.']]);
        }

        return response()->json(['success' => true, 'message' => 'OTP verified successfully.']);
    }

    private function mobile(Request $request): string
    {
        $validated = $request->validate([
            'mobile' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\-\s]{7,15}$/'],
        ]);

        return preg_replace('/[^0-9]/', '', $validated['mobile']);
    }

}