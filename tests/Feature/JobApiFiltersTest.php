<?php

namespace Tests\Feature;

use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class JobApiFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_jobs_index_can_filter_by_subcategory_partial_match(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-17 12:00:00'));

        $matchingJob = Job::create([
            'temp_id' => 'temp-shop',
            'device_id' => 'device-shop',
            'device_os' => 'android',
            'master_category' => 'Services',
            'subcategory' => 'Shop/Office/School Staff',
            'business_name' => 'Campus Store',
            'job_role' => 'Store Assistant',
            'job_type' => 'full_time',
            'salary' => 900,
            'phone_number' => '777777777',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'approved_at' => now(),
            'expires_at' => now()->addDays(5),
            'status' => 'approved',
            'plan_id' => 'plan-1',
        ]);

        Job::create([
            'temp_id' => 'temp-other',
            'device_id' => 'device-other',
            'device_os' => 'ios',
            'master_category' => 'Services',
            'subcategory' => 'Healthcare',
            'business_name' => 'Health Center',
            'job_role' => 'Nurse',
            'job_type' => 'full_time',
            'salary' => 1100,
            'phone_number' => '888888888',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'approved_at' => now(),
            'expires_at' => now()->addDays(5),
            'status' => 'approved',
            'plan_id' => 'plan-1',
        ]);

        $response = $this->getJson('/api/v1/jobs?latitude=28.591400&longitude=77.402100&radius=15&sub_categories=shop');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $matchingJob->id);
        $response->assertJsonPath('pagination.total', 1);
    }

    public function test_jobs_index_can_filter_by_job_type_and_salary_range(): void
    {
        $otherJob = Job::create([
            'temp_id' => 'temp-fulltime-low',
            'device_id' => 'device-fulltime-low',
            'device_os' => 'ios',
            'master_category' => 'Services',
            'subcategory' => 'electrical',
            'business_name' => 'Low Paying Job',
            'job_role' => 'Electrician',
            'job_type' => 'full_time',
            'salary' => 50000,
            'phone_number' => '333333333',
            'latitude' => 24.4605,
            'longitude' => 54.3705,
            'city' => 'Abu Dhabi',
            'approved_at' => now(),
            'expires_at' => now()->addDays(5),
            'status' => 'approved',
            'plan_id' => 'plan-1',
        ]);

        Job::create([
            'temp_id' => 'temp-fulltime-high',
            'device_id' => 'device-fulltime-high',
            'device_os' => 'android',
            'master_category' => 'Services',
            'subcategory' => 'plumbing',
            'business_name' => 'High Paying Job',
            'job_role' => 'Senior Plumber',
            'job_type' => 'full_time',
            'salary' => 150000,
            'phone_number' => '444444444',
            'latitude' => 24.4605,
            'longitude' => 54.3705,
            'city' => 'Abu Dhabi',
            'approved_at' => now(),
            'expires_at' => now()->addDays(5),
            'status' => 'approved',
            'plan_id' => 'plan-1',
        ]);

        $response = $this->getJson('/api/v1/jobs?latitude=24.4600&longitude=54.3700&radius=5&job_type=full_time&salary=100000');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $otherJob->id);
        $response->assertJsonMissing(['id' => 150000]);
    }

    public function test_jobs_index_can_filter_by_subcategories_and_expiry_window(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-17 12:00:00'));

        $matchingJob = Job::create([
            'temp_id' => 'temp-1',
            'device_id' => 'device-1',
            'device_os' => 'android',
            'master_category' => 'Services',
            'subcategory' => 'plumbing',
            'business_name' => 'Quick Fix',
            'job_role' => 'Plumber',
            'job_type' => 'full_time',
            'salary' => 1200,
            'phone_number' => '123456789',
            'latitude' => 24.4605,
            'longitude' => 54.3705,
            'city' => 'Abu Dhabi',
            'approved_at' => now(),
            'expires_at' => now()->addDays(2),
            'status' => 'approved',
            'plan_id' => 'plan-1',
        ]);

        Job::create([
            'temp_id' => 'temp-2',
            'device_id' => 'device-2',
            'device_os' => 'ios',
            'master_category' => 'Services',
            'subcategory' => 'electrical',
            'business_name' => 'Power Fix',
            'job_role' => 'Electrician',
            'job_type' => 'full_time',
            'salary' => 1400,
            'phone_number' => '987654321',
            'latitude' => 24.4605,
            'longitude' => 54.3705,
            'city' => 'Abu Dhabi',
            'approved_at' => now(),
            'expires_at' => now()->addDays(10),
            'status' => 'approved',
            'plan_id' => 'plan-1',
        ]);

        Job::create([
            'temp_id' => 'temp-3',
            'device_id' => 'device-3',
            'device_os' => 'android',
            'master_category' => 'Services',
            'subcategory' => 'painting',
            'business_name' => 'Paint Spot',
            'job_role' => 'Painter',
            'job_type' => 'full_time',
            'salary' => 1000,
            'phone_number' => '555555555',
            'latitude' => 24.4605,
            'longitude' => 54.3705,
            'city' => 'Abu Dhabi',
            'approved_at' => now(),
            'expires_at' => now()->addDays(2),
            'status' => 'approved',
            'plan_id' => 'plan-1',
        ]);

        Job::create([
            'temp_id' => 'temp-4',
            'device_id' => 'device-4',
            'device_os' => 'android',
            'master_category' => 'Services',
            'subcategory' => 'plumbing',
            'business_name' => 'Far Away',
            'job_role' => 'Plumber',
            'job_type' => 'full_time',
            'salary' => 1300,
            'phone_number' => '111111111',
            'latitude' => 24.5000,
            'longitude' => 54.4500,
            'city' => 'Abu Dhabi',
            'approved_at' => now(),
            'expires_at' => now()->addDays(2),
            'status' => 'approved',
            'plan_id' => 'plan-1',
        ]);

        $response = $this->getJson('/api/v1/jobs?latitude=24.4600&longitude=54.3700&radius=5&sub_categories=plumbing,electrical&is_expiry=within_3_days');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $matchingJob->id);
        $response->assertJsonPath('pagination.total', 1);
    }
}
