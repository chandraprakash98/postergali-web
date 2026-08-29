<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiExceptionHandlingAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_error_returns_consistent_422_json_structure(): void
    {
        $response = $this->postJson('/api/v1/jobs', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ])
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.');
    }

    public function test_not_found_model_returns_consistent_404_json_structure(): void
    {
        $response = $this->getJson('/api/v1/jobs/9999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.',
            ]);
    }

    public function test_not_found_endpoint_returns_consistent_404_json_structure(): void
    {
        $response = $this->getJson('/api/v1/non-existent-endpoint');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.',
            ]);
    }

    public function test_method_not_allowed_returns_consistent_405_json_structure(): void
    {
        // /api/v1/referrals/check only accepts GET
        $response = $this->putJson('/api/v1/referrals/check');

        $response->assertStatus(405)
            ->assertJson([
                'success' => false,
                'message' => 'Method not allowed.',
            ]);
    }

    public function test_unauthenticated_admin_access_redirects_or_rejects(): void
    {
        // Unauthenticated access to admin routes
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }
}
