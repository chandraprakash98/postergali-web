<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class CreditPackagePlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'plan_title' => 'Starter Pack',
                'plan_code' => 'credits_100',
                'product_id' => 'com.postergali.credits.100',
                'credits' => 100,
                'price' => 99.00,
                'currency' => 'INR',
                'plan_type' => Plan::TYPE_CONSUMABLE,
                'duration' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'plan_title' => 'Value Pack',
                'plan_code' => 'credits_220',
                'product_id' => 'com.postergali.credits.220',
                'credits' => 220,
                'price' => 199.00,
                'currency' => 'INR',
                'plan_type' => Plan::TYPE_CONSUMABLE,
                'duration' => null,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'plan_title' => 'Popular Pack',
                'plan_code' => 'credits_600',
                'product_id' => 'com.postergali.credits.600',
                'credits' => 600,
                'price' => 499.00,
                'currency' => 'INR',
                'plan_type' => Plan::TYPE_CONSUMABLE,
                'duration' => null,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'plan_title' => 'Pro Pack',
                'plan_code' => 'credits_1250',
                'product_id' => 'com.postergali.credits.1250',
                'credits' => 1250,
                'price' => 999.00,
                'currency' => 'INR',
                'plan_type' => Plan::TYPE_CONSUMABLE,
                'duration' => null,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'plan_title' => 'Business Pack',
                'plan_code' => 'credits_2600',
                'product_id' => 'com.postergali.credits.2600',
                'credits' => 2600,
                'price' => 1999.00,
                'currency' => 'INR',
                'plan_type' => Plan::TYPE_CONSUMABLE,
                'duration' => null,
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($packages as $pkg) {
            Plan::updateOrCreate(
                ['product_id' => $pkg['product_id']],
                $pkg
            );
        }
    }
}
