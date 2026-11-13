<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\Offer;
use App\Models\PosterSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosterDailyLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'posters.daily_limit_per_phone' => 2,
            'posters.limit_mode' => 'combined',
            'posters.error_message' => 'Daily poster limit reached. You can only post up to :limit posters per day.',
        ]);
    }

    private function jobPayload(string $phone, array $overrides = []): array
    {
        return array_merge([
            'temp_id' => 'temp-job-' . uniqid(),
            'device_id' => 'device-test-01',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Tech Services Co',
            'job_role' => 'Software Engineer',
            'job_type' => 'full_time',
            'phone_number' => $phone,
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
        ], $overrides);
    }

    private function offerPayload(string $mobile, array $overrides = []): array
    {
        return array_merge([
            'temp_id' => 'temp-offer-' . uniqid(),
            'device_id' => 'device-test-02',
            'device_os' => 'android',
            'master_category' => 'Retail',
            'business_name' => 'Super Store',
            'offer_details' => '50% off on all items',
            'offer_type' => 'discount',
            'mobile_number' => $mobile,
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
        ], $overrides);
    }

    public function test_user_can_post_up_to_configured_daily_limit_of_jobs(): void
    {
        $phone = '9876543210';

        // 1st job poster
        $response1 = $this->postJson('/api/v1/jobs', $this->jobPayload($phone));
        $response1->assertStatus(201);

        // 2nd job poster
        $response2 = $this->postJson('/api/v1/jobs', $this->jobPayload($phone));
        $response2->assertStatus(201);

        // 3rd job poster exceeds limit of 2
        $response3 = $this->postJson('/api/v1/jobs', $this->jobPayload($phone));
        $response3->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonValidationErrors(['phone_number'])
            ->assertJsonFragment([
                'phone_number' => ['Daily poster limit reached. You can only post up to 2 posters per day.'],
            ]);
    }

    public function test_user_can_post_up_to_configured_daily_limit_of_offers(): void
    {
        $mobile = '9876543211';

        // 1st offer
        $response1 = $this->postJson('/api/v1/offers', $this->offerPayload($mobile));
        $response1->assertStatus(201);

        // 2nd offer
        $response2 = $this->postJson('/api/v1/offers', $this->offerPayload($mobile));
        $response2->assertStatus(201);

        // 3rd offer exceeds limit of 2
        $response3 = $this->postJson('/api/v1/offers', $this->offerPayload($mobile));
        $response3->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['mobile_number'])
            ->assertJsonFragment([
                'mobile_number' => ['Daily poster limit reached. You can only post up to 2 posters per day.'],
            ]);
    }

    public function test_combined_limit_across_jobs_and_offers(): void
    {
        $phone = '9876543212';

        // 1 job
        $responseJob = $this->postJson('/api/v1/jobs', $this->jobPayload($phone));
        $responseJob->assertStatus(201);

        // 1 offer (total 2 posters)
        $responseOffer = $this->postJson('/api/v1/offers', $this->offerPayload($phone));
        $responseOffer->assertStatus(201);

        // 3rd poster as job should fail
        $responseJobBlocked = $this->postJson('/api/v1/jobs', $this->jobPayload($phone));
        $responseJobBlocked->assertStatus(422)
            ->assertJsonValidationErrors(['phone_number']);

        // 3rd poster as offer should also fail
        $responseOfferBlocked = $this->postJson('/api/v1/offers', $this->offerPayload($phone));
        $responseOfferBlocked->assertStatus(422)
            ->assertJsonValidationErrors(['mobile_number']);
    }

    public function test_phone_number_formatting_variations_count_towards_same_limit(): void
    {
        // 1st poster with 10 digits
        $response1 = $this->postJson('/api/v1/jobs', $this->jobPayload('9876543213'));
        $response1->assertStatus(201);

        // 2nd poster with +91 and spaces
        $response2 = $this->postJson('/api/v1/offers', $this->offerPayload('+91 98765 43213'));
        $response2->assertStatus(201);

        // 3rd poster with +91 without spaces should be blocked
        $response3 = $this->postJson('/api/v1/jobs', $this->jobPayload('+919876543213'));
        $response3->assertStatus(422)
            ->assertJsonValidationErrors(['phone_number']);
    }

    public function test_posters_from_yesterday_do_not_count_towards_today_limit(): void
    {
        $phone = '9876543214';

        // Insert 2 jobs created yesterday
        Job::withoutTimestamps(function () use ($phone) {
            Job::create(array_merge($this->jobPayload($phone), [
                'created_at' => Carbon::yesterday(),
                'updated_at' => Carbon::yesterday(),
            ]));
            Job::create(array_merge($this->jobPayload($phone), [
                'created_at' => Carbon::yesterday(),
                'updated_at' => Carbon::yesterday(),
            ]));
        });

        // 2 jobs should be permitted today
        $response1 = $this->postJson('/api/v1/jobs', $this->jobPayload($phone));
        $response1->assertStatus(201);

        $response2 = $this->postJson('/api/v1/jobs', $this->jobPayload($phone));
        $response2->assertStatus(201);

        // 3rd today is blocked
        $response3 = $this->postJson('/api/v1/jobs', $this->jobPayload($phone));
        $response3->assertStatus(422);
    }

    public function test_limit_is_configurable_via_config(): void
    {
        // Increase limit to 3
        config(['posters.daily_limit_per_phone' => 3]);

        $phone = '9876543215';

        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertStatus(201);
        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertStatus(201);
        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertStatus(201);

        // 4th is blocked with message reflecting 3
        $response = $this->postJson('/api/v1/jobs', $this->jobPayload($phone));
        $response->assertStatus(422)
            ->assertJsonFragment([
                'phone_number' => ['Daily poster limit reached. You can only post up to 3 posters per day.'],
            ]);
    }

    public function test_limit_can_be_disabled_with_zero(): void
    {
        // Disable limit
        config(['posters.daily_limit_per_phone' => 0]);

        $phone = '9876543216';

        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertStatus(201);
        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertStatus(201);
        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertStatus(201);
        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertStatus(201);
    }

    public function test_different_phone_numbers_have_independent_limits(): void
    {
        $phoneA = '9876543217';
        $phoneB = '9876543218';

        // Phone A reaches limit of 2
        $this->postJson('/api/v1/jobs', $this->jobPayload($phoneA))->assertStatus(201);
        $this->postJson('/api/v1/jobs', $this->jobPayload($phoneA))->assertStatus(201);
        $this->postJson('/api/v1/jobs', $this->jobPayload($phoneA))->assertStatus(422);

        // Phone B is completely unaffected and can still post
        $this->postJson('/api/v1/jobs', $this->jobPayload($phoneB))->assertStatus(201);
    }

    public function test_poster_limit_check_endpoint_reports_remaining_slots(): void
    {
        $phone = '9876543219';

        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertStatus(201);

        $this->getJson('/api/v1/posters/check?phone_number=' . $phone)
            ->assertOk()
            ->assertJson([
                'success' => true,
                'can_post' => true,
                'daily_count' => 1,
                'daily_limit' => 2,
                'remaining' => 1,
                'is_daily_limit_over' => false,
                'type' => 'all',
            ]);
    }

    public function test_poster_limit_check_endpoint_reports_configured_limit_message(): void
    {
        $phone = '9876543220';

        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertStatus(201);
        $this->postJson('/api/v1/offers', $this->offerPayload($phone))->assertStatus(201);

        $this->getJson('/api/v1/posters/check?mobile_number=' . $phone)
            ->assertOk()
            ->assertJson([
                'success' => true,
                'can_post' => false,
                'daily_count' => 2,
                'daily_limit' => 2,
                'remaining' => 0,
                'is_daily_limit_over' => true,
                'type' => 'all',
                'message' => 'Daily poster limit reached. You can only post up to 2 posters per day.',
            ]);
    }

    public function test_admin_can_configure_daily_limit_and_saved_value_controls_poster_creation(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'name' => 'Poster Admin']);

        $this->actingAs($admin)
            ->get('/admin/poster-limit-settings')
            ->assertOk()
            ->assertSee('Daily Poster Limit')
            ->assertSee('value="2"', false);

        $this->actingAs($admin)
            ->post('/admin/poster-limit-settings', ['daily_limit_per_phone' => 1])
            ->assertRedirect('/admin/poster-limit-settings')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('poster_settings', [
            'daily_limit_per_phone' => 1,
            'updated_by' => 'Poster Admin',
        ]);

        $phone = '9876543221';
        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertCreated();
        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertUnprocessable();
        $this->getJson('/api/v1/posters/daily-limit?mobile=' . $phone)
            ->assertOk()
            ->assertJsonPath('is_daily_limit_over', true)
            ->assertJsonPath('daily_count', 1)
            ->assertJsonPath('daily_limit', 1);
    }

    public function test_daily_limit_setting_validation_rejects_negative_values(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post('/admin/poster-limit-settings', ['daily_limit_per_phone' => -1])
            ->assertSessionHasErrors('daily_limit_per_phone');

        $this->assertDatabaseCount('poster_settings', 0);
    }

    public function test_per_type_limit_check_reports_over_only_when_both_types_are_at_their_limit(): void
    {
        config(['posters.limit_mode' => 'per_type']);
        PosterSetting::create(['daily_limit_per_phone' => 2]);
        $phone = '9876543222';

        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertCreated();
        $this->postJson('/api/v1/jobs', $this->jobPayload($phone))->assertCreated();

        $this->getJson('/api/v1/posters/daily-limit?mobile_number=' . $phone)
            ->assertOk()
            ->assertJsonPath('is_daily_limit_over', false)
            ->assertJsonPath('daily_count', 2)
            ->assertJsonPath('remaining', 2);

        $this->postJson('/api/v1/offers', $this->offerPayload($phone))->assertCreated();
        $this->postJson('/api/v1/offers', $this->offerPayload($phone))->assertCreated();

        $this->getJson('/api/v1/posters/daily-limit?mobile_number=' . $phone)
            ->assertOk()
            ->assertJsonPath('is_daily_limit_over', true)
            ->assertJsonPath('daily_count', 4)
            ->assertJsonPath('remaining', 0);
    }
}
