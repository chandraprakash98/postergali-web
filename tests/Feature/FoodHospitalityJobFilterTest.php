<?php

namespace Tests\Feature;

use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * FoodHospitalityJobFilterTest
 *
 * Tests all filter combinations for Food & Hospitality jobs:
 *   - sub_category  : partial / word-level matching ("Food and Hospitality")
 *   - is_expiry     : Within a day | Within 3 days | Within a week
 *   - job_type      : Full-time | Part-Time | Temporary
 *   - salary        : Less than 10000 | Less than 20000 | 21000 and above
 */
class FoodHospitalityJobFilterTest extends TestCase
{
    use RefreshDatabase;

    // Shared coordinates (Noida) used across all tests
    private const LAT = 28.5914;
    private const LNG = 77.4021;
    private const RADIUS = 10;

    /** Builds a minimal approved job at the test coordinates. */
    private function makeJob(array $overrides = []): Job
    {
        return Job::create(array_merge([
            'temp_id'       => 'test-' . uniqid(),
            'device_id'     => 'dev-test',
            'device_os'     => 'android',
            'master_category' => 'JOB',
            'business_name' => 'Test Business',
            'job_role'      => 'Test Role',
            'job_type'      => 'Full-time',
            'salary'        => 15000,
            'phone_number'  => '9000000001',
            'latitude'      => self::LAT,
            'longitude'     => self::LNG,
            'city'          => 'Noida',
            'status'        => 'approved',
            'approved_at'   => now(),
            'expires_at'    => now()->addDays(5),
            'plan_id'       => 'plan-1',
            'subcategory'   => 'Food and Hospitality',
        ], $overrides));
    }

    private function apiUrl(array $params = []): string
    {
        $base = [
            'latitude'  => self::LAT,
            'longitude' => self::LNG,
            'radius'    => self::RADIUS,
        ];

        return '/api/v1/jobs?' . http_build_query(array_merge($base, $params));
    }

    // =========================================================================
    // SUB-CATEGORY FILTER — partial / per-word matching
    // =========================================================================

    /** Exact subcategory phrase matches */
    public function test_subcategory_exact_phrase_match(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $target = $this->makeJob(['subcategory' => 'Food and Hospitality']);
        $this->makeJob(['subcategory' => 'IT and Software']);

        $response = $this->getJson($this->apiUrl(['sub_category' => 'Food and Hospitality']));

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($target->id, $data[0]['id']);
    }

    /** Only "food" is passed — should still match "Food and Hospitality" */
    public function test_subcategory_single_word_food_matches(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $food = $this->makeJob(['subcategory' => 'Food and Hospitality']);
        $this->makeJob(['subcategory' => 'IT and Software']);

        $response = $this->getJson($this->apiUrl(['sub_category' => 'food']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($food->id, $ids);
    }

    /** Only "hospitality" is passed — should match both "Food and Hospitality" and "Hospitality Services" */
    public function test_subcategory_single_word_hospitality_matches_variants(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $job1 = $this->makeJob(['subcategory' => 'Food and Hospitality']);
        $job2 = $this->makeJob(['subcategory' => 'Hospitality Services']);
        $this->makeJob(['subcategory' => 'IT and Software']);

        $response = $this->getJson($this->apiUrl(['sub_category' => 'hospitality']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($job1->id, $ids);
        $this->assertContains($job2->id, $ids);
    }

    /** "Food" as sub_category should also match "Food Service" variant */
    public function test_subcategory_food_matches_food_service(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $job = $this->makeJob(['subcategory' => 'Food Service']);
        $this->makeJob(['subcategory' => 'Retail and Sales']);

        $response = $this->getJson($this->apiUrl(['sub_category' => 'Food']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($job->id, $ids);
    }

    /** Control: non-food subcategory must NOT appear in food filter results */
    public function test_subcategory_it_software_excluded_from_food_filter(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $this->makeJob(['subcategory' => 'IT and Software', 'temp_id' => 'ctrl-it-001']);
        $food = $this->makeJob(['subcategory' => 'Food and Hospitality', 'temp_id' => 'food-001']);

        $response = $this->getJson($this->apiUrl(['sub_category' => 'food']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertNotContains(Job::where('temp_id', 'ctrl-it-001')->value('id'), $ids);
        $this->assertContains($food->id, $ids);
    }

    // =========================================================================
    // EXPIRY FILTER
    // =========================================================================

    /** "Within a day" includes jobs expiring in < 24 hours */
    public function test_expiry_within_a_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $expiresSoon  = $this->makeJob(['expires_at' => now()->addHours(20), 'temp_id' => 'exp-soon']);
        $this->makeJob(['expires_at' => now()->addDays(5), 'temp_id' => 'exp-later']);

        $response = $this->getJson($this->apiUrl(['is_expiry' => 'within_a_day']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($expiresSoon->id, $ids);
        $this->assertNotContains(Job::where('temp_id', 'exp-later')->value('id'), $ids);
    }

    /** "Within 3 days" includes jobs expiring within 72 hours */
    public function test_expiry_within_3_days(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $within3 = $this->makeJob(['expires_at' => now()->addDays(2), 'temp_id' => 'exp-3d']);
        $this->makeJob(['expires_at' => now()->addDays(10), 'temp_id' => 'exp-10d']);

        $response = $this->getJson($this->apiUrl(['is_expiry' => 'within_3_days']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($within3->id, $ids);
        $this->assertNotContains(Job::where('temp_id', 'exp-10d')->value('id'), $ids);
    }

    /** Human-readable "Within 3 days" string also works */
    public function test_expiry_within_3_days_human_readable(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $within3 = $this->makeJob(['expires_at' => now()->addDays(2), 'temp_id' => 'exp-3d-hr']);
        $this->makeJob(['expires_at' => now()->addDays(10), 'temp_id' => 'exp-10d-hr']);

        $response = $this->getJson($this->apiUrl(['is_expiry' => 'Within 3 days']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($within3->id, $ids);
    }

    /** "Within a week" includes jobs expiring within 7 days */
    public function test_expiry_within_a_week(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $within7 = $this->makeJob(['expires_at' => now()->addDays(5), 'temp_id' => 'exp-5d']);
        $this->makeJob(['expires_at' => now()->addDays(15), 'temp_id' => 'exp-15d']);

        $response = $this->getJson($this->apiUrl(['is_expiry' => 'within_a_week']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($within7->id, $ids);
        $this->assertNotContains(Job::where('temp_id', 'exp-15d')->value('id'), $ids);
    }

    // =========================================================================
    // JOB TYPE FILTER
    // =========================================================================

    /** Full-time filter returns only Full-time jobs */
    public function test_job_type_full_time_filter(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $fulltime  = $this->makeJob(['job_type' => 'Full-time',  'temp_id' => 'jt-ft']);
        $this->makeJob(['job_type' => 'Part-Time',   'temp_id' => 'jt-pt']);
        $this->makeJob(['job_type' => 'Temporary',   'temp_id' => 'jt-tmp']);

        $response = $this->getJson($this->apiUrl(['job_type' => 'Full-time']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($fulltime->id, $ids);
        $this->assertNotContains(Job::where('temp_id', 'jt-pt')->value('id'), $ids);
        $this->assertNotContains(Job::where('temp_id', 'jt-tmp')->value('id'), $ids);
    }

    /** Part-Time filter returns only Part-Time jobs */
    public function test_job_type_part_time_filter(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $parttime = $this->makeJob(['job_type' => 'Part-Time', 'temp_id' => 'jt-pt2']);
        $this->makeJob(['job_type' => 'Full-time', 'temp_id' => 'jt-ft2']);

        $response = $this->getJson($this->apiUrl(['job_type' => 'Part-Time']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($parttime->id, $ids);
        $this->assertNotContains(Job::where('temp_id', 'jt-ft2')->value('id'), $ids);
    }

    /** Temporary filter returns only Temporary jobs */
    public function test_job_type_temporary_filter(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $temporary = $this->makeJob(['job_type' => 'Temporary', 'temp_id' => 'jt-tmp2']);
        $this->makeJob(['job_type' => 'Full-time', 'temp_id' => 'jt-ft3']);

        $response = $this->getJson($this->apiUrl(['job_type' => 'Temporary']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($temporary->id, $ids);
        $this->assertNotContains(Job::where('temp_id', 'jt-ft3')->value('id'), $ids);
    }

    /** Partial match: "full" matches "Full-time" */
    public function test_job_type_partial_match(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $fulltime = $this->makeJob(['job_type' => 'Full-time', 'temp_id' => 'jt-pm-ft']);
        $this->makeJob(['job_type' => 'Part-Time', 'temp_id' => 'jt-pm-pt']);

        $response = $this->getJson($this->apiUrl(['job_type' => 'full']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($fulltime->id, $ids);
        $this->assertNotContains(Job::where('temp_id', 'jt-pm-pt')->value('id'), $ids);
    }

    // =========================================================================
    // SALARY FILTER
    // =========================================================================

    /** Named range "less_than_10000" returns only jobs with salary <= 9999 */
    public function test_salary_less_than_10000_named_range(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $low  = $this->makeJob(['salary' => 8000,  'temp_id' => 'sal-low']);
        $mid  = $this->makeJob(['salary' => 15000, 'temp_id' => 'sal-mid']);
        $high = $this->makeJob(['salary' => 45000, 'temp_id' => 'sal-high']);

        $response = $this->getJson($this->apiUrl(['salary' => 'less_than_10000']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($low->id, $ids);
        $this->assertNotContains($mid->id, $ids);
        $this->assertNotContains($high->id, $ids);
    }

    /** Human-readable "Less than 10,000" also works */
    public function test_salary_less_than_10000_human_readable(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $low  = $this->makeJob(['salary' => 8000,  'temp_id' => 'sal-low-hr']);
        $high = $this->makeJob(['salary' => 45000, 'temp_id' => 'sal-high-hr']);

        $response = $this->getJson($this->apiUrl(['salary' => 'Less than 10,000']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($low->id, $ids);
        $this->assertNotContains($high->id, $ids);
    }

    /** "less_than_20000" returns jobs with salary <= 19999 */
    public function test_salary_less_than_20000_named_range(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $low  = $this->makeJob(['salary' => 8000,  'temp_id' => 'sal-20-low']);
        $mid  = $this->makeJob(['salary' => 15000, 'temp_id' => 'sal-20-mid']);
        $high = $this->makeJob(['salary' => 45000, 'temp_id' => 'sal-20-high']);

        $response = $this->getJson($this->apiUrl(['salary' => 'less_than_20000']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($low->id, $ids);
        $this->assertContains($mid->id, $ids);
        $this->assertNotContains($high->id, $ids);
    }

    /** Human-readable "Less than 20,000" also works */
    public function test_salary_less_than_20000_human_readable(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $mid  = $this->makeJob(['salary' => 18000, 'temp_id' => 'sal-20-hr-mid']);
        $high = $this->makeJob(['salary' => 50000, 'temp_id' => 'sal-20-hr-high']);

        $response = $this->getJson($this->apiUrl(['salary' => 'Less than 20,000']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($mid->id, $ids);
        $this->assertNotContains($high->id, $ids);
    }

    /** "21000_and_above" returns only jobs with salary >= 21000 */
    public function test_salary_21000_and_above_named_range(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $low  = $this->makeJob(['salary' => 8000,  'temp_id' => 'sal-ab-low']);
        $mid  = $this->makeJob(['salary' => 15000, 'temp_id' => 'sal-ab-mid']);
        $high = $this->makeJob(['salary' => 45000, 'temp_id' => 'sal-ab-high']);

        $response = $this->getJson($this->apiUrl(['salary' => '21000_and_above']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertNotContains($low->id, $ids);
        $this->assertNotContains($mid->id, $ids);
        $this->assertContains($high->id, $ids);
    }

    /** Human-readable "21,000 and above" also works */
    public function test_salary_21000_and_above_human_readable(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $low  = $this->makeJob(['salary' => 9000,  'temp_id' => 'sal-ab-hr-low']);
        $high = $this->makeJob(['salary' => 35000, 'temp_id' => 'sal-ab-hr-high']);

        $response = $this->getJson($this->apiUrl(['salary' => '21,000 and above']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertNotContains($low->id, $ids);
        $this->assertContains($high->id, $ids);
    }

    // =========================================================================
    // COMBINED FILTERS — sub_category + expiry + job_type + salary
    // =========================================================================

    /**
     * Full-time Food & Hospitality jobs expiring within 3 days with salary < 10,000
     * Expected: fh-002 (Biryani House – salary 7500, expires in 2 days)
     */
    public function test_combined_food_fulltime_within3days_less_than_10000(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        // Matching job
        $match = $this->makeJob([
            'temp_id'     => 'comb-1-match',
            'subcategory' => 'Food and Hospitality',
            'job_type'    => 'Full-time',
            'salary'      => 7500,
            'expires_at'  => now()->addDays(2),
        ]);

        // Should not match — wrong expiry
        $this->makeJob([
            'temp_id'     => 'comb-1-exp',
            'subcategory' => 'Food and Hospitality',
            'job_type'    => 'Full-time',
            'salary'      => 7500,
            'expires_at'  => now()->addDays(10),
        ]);

        // Should not match — wrong salary
        $this->makeJob([
            'temp_id'     => 'comb-1-sal',
            'subcategory' => 'Food and Hospitality',
            'job_type'    => 'Full-time',
            'salary'      => 30000,
            'expires_at'  => now()->addDays(2),
        ]);

        // Should not match — wrong category
        $this->makeJob([
            'temp_id'     => 'comb-1-cat',
            'subcategory' => 'IT and Software',
            'job_type'    => 'Full-time',
            'salary'      => 7500,
            'expires_at'  => now()->addDays(2),
        ]);

        $response = $this->getJson($this->apiUrl([
            'sub_category' => 'Food and Hospitality',
            'is_expiry'    => 'within_3_days',
            'job_type'     => 'Full-time',
            'salary'       => 'less_than_10000',
        ]));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($match->id, $ids);
        $this->assertCount(1, $ids);
    }

    /**
     * Part-Time Food & Hospitality jobs expiring within a day with salary 21,000 and above
     */
    public function test_combined_food_parttime_within_a_day_21000_above(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $match = $this->makeJob([
            'temp_id'     => 'comb-2-match',
            'subcategory' => 'Food and Hospitality',
            'job_type'    => 'Part-Time',
            'salary'      => 25000,
            'expires_at'  => now()->addHours(16),
        ]);

        // Should not match — wrong salary range
        $this->makeJob([
            'temp_id'     => 'comb-2-low',
            'subcategory' => 'Food and Hospitality',
            'job_type'    => 'Part-Time',
            'salary'      => 6000,
            'expires_at'  => now()->addHours(16),
        ]);

        // Should not match — wrong job type
        $this->makeJob([
            'temp_id'     => 'comb-2-ft',
            'subcategory' => 'Food and Hospitality',
            'job_type'    => 'Full-time',
            'salary'      => 25000,
            'expires_at'  => now()->addHours(16),
        ]);

        $response = $this->getJson($this->apiUrl([
            'sub_category' => 'food',
            'is_expiry'    => 'within_a_day',
            'job_type'     => 'Part-Time',
            'salary'       => '21000_and_above',
        ]));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($match->id, $ids);
        $this->assertCount(1, $ids);
    }

    /**
     * Temporary Food & Hospitality jobs expiring within a week with salary < 20,000
     */
    public function test_combined_food_temporary_within_week_less_than_20000(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $match = $this->makeJob([
            'temp_id'     => 'comb-3-match',
            'subcategory' => 'Food and Hospitality',
            'job_type'    => 'Temporary',
            'salary'      => 14000,
            'expires_at'  => now()->addDays(5),
        ]);

        // Should not match — salary above range
        $this->makeJob([
            'temp_id'     => 'comb-3-sal',
            'subcategory' => 'Food and Hospitality',
            'job_type'    => 'Temporary',
            'salary'      => 28000,
            'expires_at'  => now()->addDays(5),
        ]);

        // Should not match — expiry beyond a week
        $this->makeJob([
            'temp_id'     => 'comb-3-exp',
            'subcategory' => 'Food and Hospitality',
            'job_type'    => 'Temporary',
            'salary'      => 14000,
            'expires_at'  => now()->addDays(15),
        ]);

        $response = $this->getJson($this->apiUrl([
            'sub_category' => 'hospitality',
            'is_expiry'    => 'within_a_week',
            'job_type'     => 'Temporary',
            'salary'       => 'less_than_20000',
        ]));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($match->id, $ids);
        $this->assertCount(1, $ids);
    }

    /**
     * "Food and Hospitality" phrase as sub_category correctly uses per-word matching
     * ensuring both "food" word and "hospitality" word each yield hits.
     */
    public function test_subcategory_phrase_matches_on_any_word(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00'));

        $foodAndHospitality = $this->makeJob(['subcategory' => 'Food and Hospitality', 'temp_id' => 'ph-1']);
        $foodService        = $this->makeJob(['subcategory' => 'Food Service',         'temp_id' => 'ph-2']);
        $hospitalityServices= $this->makeJob(['subcategory' => 'Hospitality Services', 'temp_id' => 'ph-3']);
        $retailSales        = $this->makeJob(['subcategory' => 'Retail and Sales',     'temp_id' => 'ph-4']);

        $response = $this->getJson($this->apiUrl(['sub_category' => 'Food and Hospitality']));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');

        // All three food/hospitality variants must match
        $this->assertContains($foodAndHospitality->id, $ids);
        $this->assertContains($foodService->id, $ids);
        $this->assertContains($hospitalityServices->id, $ids);

        // Retail must NOT match
        $this->assertNotContains($retailSales->id, $ids);
    }
}
