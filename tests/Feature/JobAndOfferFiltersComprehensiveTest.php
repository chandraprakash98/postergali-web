<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\Offer;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class JobAndOfferFiltersComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    private const LAT = 28.5914;
    private const LNG = 77.4021;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-08-29 12:00:00'));

        $plan = Plan::create([
            'plan_title' => 'Test Plan',
            'duration' => '30 days',
            'price' => 499,
        ]);

        // Job 1: Food, full_time, salary 8000, expires in 1 day
        Job::create([
            'temp_id' => 'job-1',
            'device_id' => 'dev-1',
            'device_os' => 'android',
            'master_category' => 'JOB',
            'subcategory' => 'Food and Hospitality',
            'business_name' => 'Burger Queen',
            'job_role' => 'Chef',
            'job_type' => 'full_time',
            'salary' => 8000,
            'phone_number' => '9998887771',
            'latitude' => self::LAT,
            'longitude' => self::LNG,
            'city' => 'Noida',
            'status' => 'approved',
            'approved_at' => now(),
            'expires_at' => now()->addDays(1),
            'plan_id' => (string) $plan->id,
        ]);

        // Job 2: IT, part_time, salary 18000, expires in 3 days
        Job::create([
            'temp_id' => 'job-2',
            'device_id' => 'dev-2',
            'device_os' => 'ios',
            'master_category' => 'JOB',
            'subcategory' => 'IT and Software',
            'business_name' => 'Tech Code Labs',
            'job_role' => 'QA Tester',
            'job_type' => 'part_time',
            'salary' => 18000,
            'phone_number' => '9998887772',
            'latitude' => self::LAT,
            'longitude' => self::LNG,
            'city' => 'Noida',
            'status' => 'approved',
            'approved_at' => now(),
            'expires_at' => now()->addDays(3),
            'plan_id' => (string) $plan->id,
        ]);

        // Job 3: Shop, temporary, salary 30000, expires in 7 days
        Job::create([
            'temp_id' => 'job-3',
            'device_id' => 'dev-3',
            'device_os' => 'android',
            'master_category' => 'JOB',
            'subcategory' => 'Shop/Office/School Staff',
            'business_name' => 'Supermarket Express',
            'job_role' => 'Supervisor',
            'job_type' => 'temporary',
            'salary' => 30000,
            'phone_number' => '9998887773',
            'latitude' => self::LAT,
            'longitude' => self::LNG,
            'city' => 'Noida',
            'status' => 'approved',
            'approved_at' => now(),
            'expires_at' => now()->addDays(7),
            'plan_id' => (string) $plan->id,
        ]);

        // Offer 1: Food, discount, expires in 1 day
        Offer::create([
            'temp_id' => 'offer-1',
            'device_id' => 'dev-1',
            'device_os' => 'android',
            'master_category' => 'OFFER',
            'subcategory' => 'Food and Hospitality',
            'business_name' => 'Pizza House',
            'offer_details' => '50% off on all pizzas',
            'offer_type' => 'discount',
            'mobile_number' => '9998887771',
            'latitude' => self::LAT,
            'longitude' => self::LNG,
            'city' => 'Noida',
            'status' => 'approved',
            'approved_at' => now(),
            'expires_at' => now()->addDays(1),
            'plan_id' => (string) $plan->id,
        ]);

        // Offer 2: Shop, combo, expires in 4 days
        Offer::create([
            'temp_id' => 'offer-2',
            'device_id' => 'dev-2',
            'device_os' => 'ios',
            'master_category' => 'OFFER',
            'subcategory' => 'Shop/Office/School Staff',
            'business_name' => 'Book Haven',
            'offer_details' => 'Buy 1 Get 1 free',
            'offer_type' => 'combo',
            'mobile_number' => '9998887772',
            'latitude' => self::LAT,
            'longitude' => self::LNG,
            'city' => 'Noida',
            'status' => 'approved',
            'approved_at' => now(),
            'expires_at' => now()->addDays(4),
            'plan_id' => (string) $plan->id,
        ]);
    }

    public function test_jobs_filter_by_subcategory_and_salary_under_10000(): void
    {
        $response = $this->getJson('/api/v1/jobs?latitude=' . self::LAT . '&longitude=' . self::LNG . '&radius=10&sub_categories=food&salary=less_than_10000');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.temp_id', 'job-1');
    }

    public function test_jobs_filter_by_salary_21000_and_above(): void
    {
        $response = $this->getJson('/api/v1/jobs?latitude=' . self::LAT . '&longitude=' . self::LNG . '&radius=10&salary=21000_and_above');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.temp_id', 'job-3');
    }

    public function test_jobs_filter_by_job_type_and_expiry_window(): void
    {
        $response = $this->getJson('/api/v1/jobs?latitude=' . self::LAT . '&longitude=' . self::LNG . '&radius=10&job_type=part_time&is_expiry=within_3_days');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.temp_id', 'job-2');
    }

    public function test_jobs_multiple_combined_filters(): void
    {
        $response = $this->getJson('/api/v1/jobs?latitude=' . self::LAT . '&longitude=' . self::LNG . '&radius=10&sub_categories=Food%20and%20Hospitality&job_type=full_time&salary=less_than_10000&is_expiry=within_a_day');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.temp_id', 'job-1');
    }

    public function test_jobs_rejects_unsupported_query_filter_with_422(): void
    {
        $response = $this->getJson('/api/v1/jobs?latitude=' . self::LAT . '&longitude=' . self::LNG . '&invalid_filter_key=123');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['filters']);
    }

    public function test_jobs_rejects_invalid_expiry_format_with_422(): void
    {
        $response = $this->getJson('/api/v1/jobs?latitude=' . self::LAT . '&longitude=' . self::LNG . '&is_expiry=totally_invalid_time_frame');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['is_expiry']);
    }

    public function test_jobs_rejects_invalid_salary_format_with_422(): void
    {
        $response = $this->getJson('/api/v1/jobs?latitude=' . self::LAT . '&longitude=' . self::LNG . '&salary=invalid_salary_text');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['salary']);
    }

    public function test_offers_filter_by_offer_type_and_expiry(): void
    {
        $response = $this->getJson('/api/v1/offers?latitude=' . self::LAT . '&longitude=' . self::LNG . '&radius=10&offer_type=discount&is_expiry=within_a_day');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.temp_id', 'offer-1');
    }

    public function test_offers_filter_by_subcategory(): void
    {
        $response = $this->getJson('/api/v1/offers?latitude=' . self::LAT . '&longitude=' . self::LNG . '&radius=10&sub_categories=Shop');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.temp_id', 'offer-2');
    }

    public function test_offers_rejects_unsupported_query_filter_with_422(): void
    {
        $response = $this->getJson('/api/v1/offers?latitude=' . self::LAT . '&longitude=' . self::LNG . '&unsupported_key=test');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['filters']);
    }

    public function test_job_search_by_phone_number_and_location(): void
    {
        $response = $this->getJson('/api/v1/jobs/search?phone_number=9998887771&latitude=' . self::LAT . '&longitude=' . self::LNG);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.temp_id', 'job-1');
    }

    public function test_jobs_filter_with_distance_alias_and_hyphenated_job_type_and_currency_salary(): void
    {
        $response = $this->getJson('/api/v1/jobs?latitude=' . self::LAT . '&longitude=' . self::LNG . '&distance=10&job_type=Full-time&salary=' . urlencode('Less than ₹10,000'));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.temp_id', 'job-1');
    }

    public function test_offers_filter_with_distance_alias_and_expiry_alias(): void
    {
        $response = $this->getJson('/api/v1/offers?latitude=' . self::LAT . '&longitude=' . self::LNG . '&distance=10&expiry=Within%20a%20day');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.temp_id', 'offer-1');
    }

    public function test_jobs_filter_with_typo_tolerant_maintenance_subcategory(): void
    {
        $response = $this->getJson('/api/v1/jobs?latitude=' . self::LAT . '&longitude=' . self::LNG . '&sub_categories=' . urlencode('Housekeeping and Maintanence,Food and Hospitality'));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.temp_id', 'job-1');
    }

    public function test_offer_search_by_device_id(): void
    {
        $response = $this->getJson('/api/v1/offers/search?device_id=dev-2');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.temp_id', 'offer-2');
    }

    public function test_offers_filter_by_subcategory_expiry_and_distance_band_0_to_5_km(): void
    {
        $response = $this->getJson('/api/v1/offers?latitude=' . self::LAT . '&longitude=' . self::LNG . '&distance=' . urlencode('0-5 Km') . '&sub_categories=Food&expiry=Within%20a%20day');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.temp_id', 'offer-1')
            ->assertJsonPath('min_distance_km', 0)
            ->assertJsonPath('max_distance_km', 5);
    }

    public function test_offers_filter_distance_band_5_to_10_km_excludes_closer_records(): void
    {
        $response = $this->getJson('/api/v1/offers?latitude=' . self::LAT . '&longitude=' . self::LNG . '&distance=' . urlencode('5-10 Km'));

        $response->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('min_distance_km', 5)
            ->assertJsonPath('max_distance_km', 10);
    }
}
