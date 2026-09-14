<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Job;
use App\Models\Offer;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CustomerPosterAdsFilterTest extends TestCase
{
    use RefreshDatabase;

    private const LAT = 28.5914;
    private const LNG = 77.4021;
    private const MOBILE = '9998887771';

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-08-29 12:00:00'));

        $customer = Customer::create([
            'mobile' => self::MOBILE,
            'fcm' => 'fcm-token-123',
        ]);

        $plan = Plan::create([
            'plan_title' => 'Test Plan',
            'duration' => '30 days',
            'price' => 499,
        ]);

        // Job 1: Food, full_time, salary 8000, expires in 1 day, approved
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
            'phone_number' => self::MOBILE,
            'latitude' => self::LAT,
            'longitude' => self::LNG,
            'city' => 'Noida',
            'status' => 'approved',
            'approved_at' => now(),
            'expires_at' => now()->addDays(1),
            'plan_id' => (string) $plan->id,
        ]);

        // Job 2: IT, part_time, salary 25000, expires in 5 days, pending
        Job::create([
            'temp_id' => 'job-2',
            'device_id' => 'dev-1',
            'device_os' => 'android',
            'master_category' => 'JOB',
            'subcategory' => 'IT and Software',
            'business_name' => 'Code Works',
            'job_role' => 'Developer',
            'job_type' => 'part_time',
            'salary' => 25000,
            'phone_number' => self::MOBILE,
            'latitude' => self::LAT,
            'longitude' => self::LNG,
            'city' => 'Noida',
            'status' => 'pending',
            'approved_at' => null,
            'expires_at' => now()->addDays(5),
            'plan_id' => (string) $plan->id,
        ]);

        // Offer 1: Food, discount, expires in 1 day, approved
        Offer::create([
            'temp_id' => 'offer-1',
            'device_id' => 'dev-1',
            'device_os' => 'android',
            'master_category' => 'OFFER',
            'subcategory' => 'Food and Hospitality',
            'business_name' => 'Burger Queen Offers',
            'offer_details' => '50% off on combos',
            'offer_type' => 'discount',
            'mobile_number' => self::MOBILE,
            'latitude' => self::LAT,
            'longitude' => self::LNG,
            'city' => 'Noida',
            'status' => 'approved',
            'approved_at' => now(),
            'expires_at' => now()->addDays(1),
            'plan_id' => (string) $plan->id,
        ]);

        // Offer 2: Retail, combo, expires in 6 days, approved
        Offer::create([
            'temp_id' => 'offer-2',
            'device_id' => 'dev-1',
            'device_os' => 'android',
            'master_category' => 'OFFER',
            'subcategory' => 'Shop/Office/School Staff',
            'business_name' => 'Stationery World',
            'offer_details' => 'Buy 2 Get 1 Free',
            'offer_type' => 'combo',
            'mobile_number' => self::MOBILE,
            'latitude' => self::LAT,
            'longitude' => self::LNG,
            'city' => 'Noida',
            'status' => 'approved',
            'approved_at' => now(),
            'expires_at' => now()->addDays(6),
            'plan_id' => (string) $plan->id,
        ]);
    }

    public function test_customer_can_fetch_all_posters_without_filters(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'jobs')
            ->assertJsonCount(2, 'offers');
    }

    public function test_my_posters_filter_by_subcategory(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&sub_categories=Food');

        $response->assertOk()
            ->assertJsonCount(1, 'jobs')
            ->assertJsonPath('jobs.0.temp_id', 'job-1')
            ->assertJsonCount(1, 'offers')
            ->assertJsonPath('offers.0.temp_id', 'offer-1');
    }

    public function test_my_posters_filter_by_expiry_window(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&is_expiry=within_a_day');

        $response->assertOk()
            ->assertJsonCount(1, 'jobs')
            ->assertJsonPath('jobs.0.temp_id', 'job-1')
            ->assertJsonCount(1, 'offers')
            ->assertJsonPath('offers.0.temp_id', 'offer-1');
    }

    public function test_my_posters_filter_by_job_type(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&job_type=full_time');

        $response->assertOk()
            ->assertJsonCount(1, 'jobs')
            ->assertJsonPath('jobs.0.temp_id', 'job-1');
    }

    public function test_my_posters_filter_by_salary(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&salary=less_than_10000');

        $response->assertOk()
            ->assertJsonCount(1, 'jobs')
            ->assertJsonPath('jobs.0.temp_id', 'job-1');

        $highSalary = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&salary=21000_and_above');
        $highSalary->assertOk()
            ->assertJsonCount(1, 'jobs')
            ->assertJsonPath('jobs.0.temp_id', 'job-2');
    }

    public function test_my_posters_filter_by_offer_type(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&offer_type=combo');

        $response->assertOk()
            ->assertJsonCount(1, 'offers')
            ->assertJsonPath('offers.0.temp_id', 'offer-2');
    }

    public function test_my_posters_filter_by_type_job_only(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&type=job');

        $response->assertOk()
            ->assertJsonCount(2, 'jobs')
            ->assertJsonCount(0, 'offers');
    }

    public function test_my_posters_filter_by_type_offer_only(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&type=offer');

        $response->assertOk()
            ->assertJsonCount(0, 'jobs')
            ->assertJsonCount(2, 'offers');
    }

    public function test_my_posters_filter_by_status(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&status=pending');

        $response->assertOk()
            ->assertJsonCount(1, 'jobs')
            ->assertJsonPath('jobs.0.temp_id', 'job-2')
            ->assertJsonCount(0, 'offers');
    }

    public function test_my_posters_location_and_distance_filters(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&latitude=' . self::LAT . '&longitude=' . self::LNG . '&distance=10');

        $response->assertOk()
            ->assertJsonCount(2, 'jobs')
            ->assertJsonCount(2, 'offers')
            ->assertJsonPath('radius_km', 10);
    }

    public function test_my_posters_combined_filters(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&sub_categories=Food&job_type=full_time&salary=less_than_10000&is_expiry=within_a_day');

        $response->assertOk()
            ->assertJsonCount(1, 'jobs')
            ->assertJsonPath('jobs.0.temp_id', 'job-1');
    }

    public function test_my_posters_rejects_unsupported_filter_parameter_with_422(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&invalid_key=foo');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['filters']);
    }

    public function test_my_posters_rejects_invalid_expiry_with_422(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&is_expiry=invalid_window');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['is_expiry']);
    }

    public function test_my_posters_rejects_invalid_salary_with_422(): void
    {
        $response = $this->getJson('/api/v1/customers/poster-ads?mobile=' . self::MOBILE . '&salary=invalid_salary');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['salary']);
    }

    public function test_my_posters_accessible_via_aliases(): void
    {
        $res1 = $this->getJson('/api/v1/customers/my-posters?mobile=' . self::MOBILE . '&sub_categories=Food');
        $res1->assertOk()
            ->assertJsonCount(1, 'jobs')
            ->assertJsonCount(1, 'offers');

        $res2 = $this->getJson('/api/v1/my-posters?mobile=' . self::MOBILE . '&sub_categories=Food');
        $res2->assertOk()
            ->assertJsonCount(1, 'jobs')
            ->assertJsonCount(1, 'offers');
    }

    public function test_my_posters_with_customer_id(): void
    {
        $customer = Customer::where('mobile', self::MOBILE)->first();

        $response = $this->getJson('/api/v1/customers/poster-ads?customer_id=' . $customer->customer_id);

        $response->assertOk()
            ->assertJsonPath('customer_id', $customer->customer_id)
            ->assertJsonCount(2, 'jobs')
            ->assertJsonCount(2, 'offers');
    }
}
