<?php

namespace Database\Seeders;

use App\Models\CreditPack;
use Illuminate\Database\Seeder;

class CreditPackSeeder extends Seeder
{
    /**
     * Seed predefined consumable credit packs.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter Pack',
                'pack_code' => 'credits_100',
                'product_id' => 'com.postergali.credits.100',
                'credits' => 100,
                'price' => 99.00,
                'currency' => 'INR',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Value Pack',
                'pack_code' => 'credits_220',
                'product_id' => 'com.postergali.credits.220',
                'credits' => 220,
                'price' => 199.00,
                'currency' => 'INR',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Popular Pack',
                'pack_code' => 'credits_600',
                'product_id' => 'com.postergali.credits.600',
                'credits' => 600,
                'price' => 499.00,
                'currency' => 'INR',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Pro Pack',
                'pack_code' => 'credits_1250',
                'product_id' => 'com.postergali.credits.1250',
                'credits' => 1250,
                'price' => 999.00,
                'currency' => 'INR',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Business Pack',
                'pack_code' => 'credits_2600',
                'product_id' => 'com.postergali.credits.2600',
                'credits' => 2600,
                'price' => 1999.00,
                'currency' => 'INR',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($packages as $pkg) {
            CreditPack::updateOrCreate(
                ['pack_code' => $pkg['pack_code']],
                $pkg
            );
        }
    }
}
