<?php

namespace Tests\Feature;

use App\Models\OtpVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OtpApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_an_otp_through_the_configured_provider(): void
    {
        Http::fake([
            '*' => Http::response([
                'isSuccess' => true,
                'returnMessage' => 'You have sucessfully uploaded 1 no',
                'token' => null,
                'data' => '7982553609-provider-token',
            ]),
        ]);

        $response = $this->postJson('/api/v1/auth/otp/send', ['mobile' => '+91 79825-53609']);

        $response->assertOk()->assertJson([
            'success' => true,
            'message' => 'OTP sent successfully.',
        ]);
        $this->assertDatabaseHas('otp_verifications', [
            'mobile' => '917982553609',
            'provider_token' => '7982553609-provider-token',
        ]);
        Http::assertSent(function ($request): bool {
            $data = $request->data();

            return str_starts_with($request->url(), config('services.bulk_sms.url'))
                && $data['dest'] === '917982553609'
                && preg_match('/^Postergali - Use OTP \d{6} to complete your verification\. Please do not share this OTP with anyone\.\nUNITYGRID PVT LTD$/', $data['msg']) === 1;
        });
    }

    public function test_it_does_not_store_an_otp_when_provider_rejects_it(): void
    {
        Http::fake(['*' => Http::response(['isSuccess' => false], 200)]);

        $response = $this->postJson('/api/v1/auth/otp/send', ['mobile' => '7982553609']);

        $response->assertStatus(502)->assertJson(['success' => false]);
        $this->assertDatabaseCount('otp_verifications', 0);
    }

    public function test_it_verifies_an_otp_once_and_rejects_invalid_codes(): void
    {
        OtpVerification::create([
            'mobile' => '7982553609',
            'code_hash' => Hash::make('262626'),
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->postJson('/api/v1/auth/otp/verify', [
            'mobile' => '7982553609',
            'otp' => '111111',
        ])->assertStatus(422)->assertJsonValidationErrors(['otp']);

        $this->postJson('/api/v1/auth/otp/verify', [
            'mobile' => '7982553609',
            'otp' => '262626',
        ])->assertOk()->assertJson(['success' => true]);

        $this->postJson('/api/v1/auth/otp/verify', [
            'mobile' => '7982553609',
            'otp' => '262626',
        ])->assertStatus(422)->assertJsonValidationErrors(['otp']);
    }
}