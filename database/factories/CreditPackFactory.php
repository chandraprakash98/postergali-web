<?php

namespace Database\Factories;

use App\Models\CreditPack;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditPackFactory extends Factory
{
    protected $model = CreditPack::class;

    public function definition(): array
    {
        $credits = $this->faker->randomElement([100, 220, 600, 1250, 2600]);
        $price = match ($credits) {
            100 => 99.00,
            220 => 199.00,
            600 => 499.00,
            1250 => 999.00,
            2600 => 1999.00,
            default => 299.00,
        };

        return [
            'name' => "Pack {$credits}",
            'pack_code' => "credits_{$credits}_" . bin2hex(random_bytes(3)),
            'product_id' => "com.postergali.credits.{$credits}." . bin2hex(random_bytes(3)),
            'credits' => $credits,
            'price' => $price,
            'currency' => 'INR',
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
