<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Job;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPosterCreditRefundTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejecting_full_credit_poster_refunds_once(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = Customer::create(['mobile' => '9560213954']);
        CustomerCredit::create([
            'customer_id' => $customer->customer_id,
            'balance' => 250,
        ]);

        $job = Job::create([
            'temp_id' => 'temp-job-refund',
            'device_id' => 'device-job-refund',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Refund Test Business',
            'job_role' => 'Developer',
            'job_type' => 'full_time',
            'salary' => 1200,
            'phone_number' => $customer->mobile,
            'latitude' => 24.4539,
            'longitude' => 54.3773,
            'city' => 'Abu Dhabi',
            'status' => 'pending',
            'plan_id' => 'plan-1',
        ]);

        $otherCustomer = Customer::create(['mobile' => '9560213955']);
        Payment::create([
            'customer_id' => $otherCustomer->customer_id,
            'transaction_id' => 'transaction-wrong-customer',
            'job_or_offer_id' => $job->id,
            'item_type' => 'Services',
            'credit_mode' => 'full_credit',
            'payment_type' => Payment::TYPE_FULL_CREDIT,
            'total_amount' => 50,
            'credit_amount' => 50,
            'payment_status' => Payment::STATUS_COMPLETED,
        ]);

        Payment::create([
            'customer_id' => $customer->customer_id,
            'transaction_id' => 'transaction-refund-test',
            'job_or_offer_id' => $job->id,
            'item_type' => 'Services',
            'credit_mode' => 'full_credit',
            'payment_type' => Payment::TYPE_FULL_CREDIT,
            'total_amount' => 100,
            'credit_amount' => 100,
            'payment_status' => Payment::STATUS_COMPLETED,
        ]);

        $this->actingAs($admin)
            ->postJson('/admin/ads/job/' . $job->id . '/status', [
                'status' => 'rejected',
                'comment' => 'Invalid poster',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('customer_credits', [
            'customer_id' => $customer->customer_id,
            'balance' => 350,
        ]);
        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'transaction-refund-test',
            'payment_status' => Payment::STATUS_REFUNDED,
        ]);
        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'transaction-wrong-customer',
            'payment_status' => Payment::STATUS_COMPLETED,
        ]);

        $this->actingAs($admin)
            ->postJson('/admin/ads/job/' . $job->id . '/status', [
                'status' => 'rejected',
                'comment' => 'Still invalid',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('customer_credits', [
            'customer_id' => $customer->customer_id,
            'balance' => 350,
        ]);
    }
}