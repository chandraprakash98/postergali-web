<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\Offer;
use App\Models\UgcReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UgcReportTest extends TestCase
{
    use RefreshDatabase;

    private function createJob(): Job
    {
        return Job::create([
            'temp_id' => 'reported-job',
            'device_id' => 'device-job',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Reported Business',
            'job_role' => 'Designer',
            'phone_number' => '9876543210',
            'latitude' => 28.6139,
            'longitude' => 77.2090,
            'city' => 'Delhi',
            'plan_id' => 'plan-1',
        ]);
    }

    private function createOffer(): Offer
    {
        return Offer::create([
            'temp_id' => 'reported-offer',
            'device_id' => 'device-offer',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Reported Offer Business',
            'offer_details' => 'Special offer',
            'mobile_number' => '9876543210',
            'latitude' => 28.6139,
            'longitude' => 77.2090,
            'city' => 'Delhi',
            'plan_id' => 'plan-1',
        ]);
    }

    public function test_report_endpoint_stores_report_and_sends_notification_email(): void
    {
        Mail::shouldReceive('raw')->once()->withArgs(function (string $body, callable $callback): bool {
            return str_contains($body, '"content_type": "job"')
                && str_contains($body, '"reason": "Spam"')
                && is_callable($callback);
        });

        $job = $this->createJob();

        $response = $this->postJson('/api/ugc/report', [
            'content_id' => $job->id,
            'content_type' => 'job',
            'reason' => 'Spam',
            'details' => 'Repeated misleading content',
            'author_id' => 'poster-123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Report submitted successfully.');

        $this->assertDatabaseHas('ugc_reports', [
            'content_id' => $job->id,
            'content_type' => 'job',
            'reason' => 'Spam',
            'details' => 'Repeated misleading content',
            'author_id' => 'poster-123',
        ]);
    }

    public function test_report_endpoint_accepts_offer_posters(): void
    {
        Mail::shouldReceive('raw')->once();
        $offer = $this->createOffer();

        $this->postJson('/api/ugc/report', [
            'content_id' => $offer->id,
            'content_type' => 'offer',
            'reason' => 'Inappropriate content',
        ])->assertCreated();

        $this->assertDatabaseHas('ugc_reports', [
            'content_id' => $offer->id,
            'content_type' => 'offer',
            'reason' => 'Inappropriate content',
            'details' => null,
            'author_id' => null,
        ]);
    }

    public function test_report_endpoint_rejects_invalid_or_missing_posters(): void
    {
        $this->postJson('/api/ugc/report', [
            'content_id' => 1,
            'content_type' => 'user',
            'reason' => 'Spam',
        ])->assertUnprocessable();

        $this->postJson('/api/ugc/report', [
            'content_id' => 999,
            'content_type' => 'job',
            'reason' => 'Spam',
        ])->assertNotFound();

        $this->assertDatabaseCount('ugc_reports', 0);
    }

    public function test_admin_can_review_reports_and_non_admins_cannot(): void
    {
        $job = $this->createJob();
        $report = UgcReport::create([
            'content_id' => $job->id,
            'content_type' => 'job',
            'reason' => 'Spam',
            'details' => 'Please review this listing',
            'author_id' => 'poster-123',
        ]);

        $this->get('/admin/ugc-reports')->assertRedirect('/admin/login');

        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/ugc-reports')
            ->assertOk()
            ->assertSee('Reported Posters')
            ->assertSee('Reported Business')
            ->assertSee('Please review this listing')
            ->assertSee(route('admin.ad.show', ['type' => 'job', 'id' => $job->id]));
    }
}
