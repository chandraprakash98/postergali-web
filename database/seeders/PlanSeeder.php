<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Seed poster duration plans for PosterMan chatbot and ad publishing.
     */
    public function run(): void
    {
        $plans = [
            [
                'plan_title' => '1 Day Flash Plan',
                'duration' => '1 days',
                'price' => 49.00,
            ],
            [
                'plan_title' => '2 Days Weekend Plan',
                'duration' => '2 days',
                'price' => 89.00,
            ],
            [
                'plan_title' => '10 Days Booster',
                'duration' => '10 days',
                'price' => 249.00,
            ],
            [
                'plan_title' => 'Basic (30 Days)',
                'duration' => '30 days',
                'price' => 499.00,
            ],
            [
                'plan_title' => 'Pro (90 Days)',
                'duration' => '90 days',
                'price' => 1299.00,
            ],
            [
                'plan_title' => 'Premium (180 Days)',
                'duration' => '180 days',
                'price' => 2499.00,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(
                ['duration' => $plan['duration']],
                $plan
            );
        }
    }
}
