<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        $credits = $this->faker->randomElement([100, 220, 600, 1250, 2600]);

        return [
            'plan_title' => $this->faker->words(2, true) . ' Pack',
            'plan_code' => 'credits_' . $credits . '_' . $this->faker->unique()->numberBetween(100, 999),
            'product_id' => 'com.postergali.credits.' . $credits . '.' . $this->faker->unique()->numberBetween(100, 999),
            'credits' => $credits,
            'price' => $this->faker->randomFloat(2, 49, 1999),
            'currency' => 'INR',
            'plan_type' => Plan::TYPE_CONSUMABLE,
            'duration' => null,
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }

    public function durationTier(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_type' => Plan::TYPE_POSTER_DURATION,
            'duration' => '30 days',
            'credits' => null,
            'plan_code' => null,
            'product_id' => null,
        ]);
    }
}
