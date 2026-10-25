<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreditPackagePlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(CreditPackSeeder::class);
    }
}
