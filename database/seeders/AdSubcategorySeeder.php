<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdSubcategorySeeder extends Seeder
{
    /**
     * Seed the ad_subcategories table with job and offer subcategories.
     * These values were previously hard-coded in the admin ad-details view.
     */
    public function run(): void
    {
        $now = now();

      $jobSubcategories = [
    'Retail and Shop Jobs',
    'Food and Hospitality',
    'Delivery and Logistics',
    'Office and Admin',
    'Skilled Workers',
    'Housekeeping and Maintenance',
    'Creative and Digital',
    'HealthCare and Care',
    'Education and Training',
    'Construction and Labor',
];

$offerSubcategories = [
    'Food and Hospitality',
    'Fashion and Apparel',
    'Salon and Beauty',
    'Electronics',
    'Grocery and Supermarket',
    'Travel and Tourism',
    'Cafe and Beverages',
    'Gym and Fitness',
    'Health and Wellness',
    'Education and Courses',
    'Entertainment',
    'Home and Furniture',
];

        $rows = [];

        foreach ($jobSubcategories as $index => $name) {
            $rows[] = [
                'type'       => 'job',
                'name'       => $name,
                'sort_order' => $index + 1,
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($offerSubcategories as $index => $name) {
            $rows[] = [
                'type'       => 'offer',
                'name'       => $name,
                'sort_order' => $index + 1,
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Use upsert to keep seeder idempotent (safe to re-run)
        DB::table('ad_subcategories')->upsert(
            $rows,
            ['type', 'name'],       // unique keys to match on
            ['sort_order', 'is_active', 'updated_at'] // columns to update if exists
        );
    }
}
