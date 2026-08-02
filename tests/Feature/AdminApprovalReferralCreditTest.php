<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Job;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApprovalReferralCreditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_approval_adds_credit_and_marks_referral_success(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $referrer = Customer::create([
            'mobile' => '9560213952',
        ]);

        CustomerCredit::create([
            'customer_id' => $referrer->customer_id,
            'balance' => 250,
        ]);

        $job = Job::create([
            'temp_id' => 'temp-job-approval',
            'device_id' => 'device-job-approval',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Test Business',
            'job_role' => 'Developer',
            'job_type' => 'full_time',
            'salary' => 1200,
            'phone_number' => '9560213954',
            'latitude' => 24.4539,
            'longitude' => 54.3773,
            'city' => 'Abu Dhabi',
            'status' => 'pending',
            'plan_id' => 'plan-1',
        ]);

        Referral::create([
            'referrer_name' => 'Chandra Prakash',
            'referrer_mobile' => '9560213952',
            'referral_name' => 'Ankit',
            'referral_mobile' => '9560213954',
            'status' => 'IN PROGRESS',
        ]);

        $this->actingAs($admin)
            ->postJson('/admin/ads/job/' . $job->id . '/status', [
                'status' => 'approved',
                'comment' => 'Looks good',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'status' => 'approved',
        ]);

        $credit = CustomerCredit::where('customer_id', $referrer->customer_id)->first();
        $this->assertSame(350.0, (float) $credit->balance);

        $this->assertDatabaseHas('referrals', [
            'referral_mobile' => '9560213954',
            'status' => 'Success',
        ]);
    }
}
