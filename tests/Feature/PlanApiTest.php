<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_plans_are_returned_in_ascending_duration_order(): void
    {
        foreach ([30, 10, 1, 2] as $days) {
            Plan::create([
                'plan_title' => "{$days} day plan",
                'duration' => "{$days} days",
                'price' => $days * 10,
            ]);
        }

        $response = $this->getJson('/api/v1/plans');

        $response->assertOk()
            ->assertJsonPath('*.duration', [
                '1 days',
                '2 days',
                '10 days',
                '30 days',
            ]);
    }
}
