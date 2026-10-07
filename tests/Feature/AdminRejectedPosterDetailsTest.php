<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRejectedPosterDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_expiry_date_is_hidden_and_rejected_poster_cannot_be_approved(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $job = Job::create([
            'temp_id' => 'rejected-job',
            'device_id' => 'test-device',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Rejected Business',
            'job_role' => 'Developer',
            'phone_number' => '9876543210',
            'latitude' => 28.6139,
            'longitude' => 77.2090,
            'city' => 'Delhi',
            'status' => 'rejected',
            'plan_id' => 'plan-1',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.ad.show', ['type' => 'job', 'id' => $job->id]))
            ->assertOk()
            ->assertDontSee('Expiry Date')
            ->assertDontSee('name="expires_at"', false)
            ->assertSee('value="approved"  disabled', false);
    }
}
