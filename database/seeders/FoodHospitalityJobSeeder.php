<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;

/**
 * FoodHospitalityJobSeeder
 *
 * Creates a comprehensive set of Food & Hospitality job listings to test
 * all filter combinations:
 *
 *  sub_category  : "Food and Hospitality" (and variants like "Food Service", "Hospitality Services")
 *  is_expiry     : within a day, within 3 days, within a week, beyond a week
 *  job_type      : Full-time, Part-Time, Temporary
 *  salary        : < 10000 | < 20000 | 21000 and above
 *
 * Run with: php artisan db:seed --class=FoodHospitalityJobSeeder
 */
class FoodHospitalityJobSeeder extends Seeder
{
    /** Shared lat/lng for Noida — use this location when hitting the API in tests */
    public const TEST_LAT = 28.5914;
    public const TEST_LNG = 77.4021;

    public function run(): void
    {
        $base = [
            'device_id'       => 'test-device-fh',
            'device_os'       => 'android',
            'master_category' => 'JOB',
            'phone_number'    => '9000000001',
            'latitude'        => self::TEST_LAT,
            'longitude'       => self::TEST_LNG,
            'city'            => 'Noida',
            'status'          => 'approved',
            'approved_at'     => now(),
            'plan_id'         => 'plan-test',
        ];

        $jobs = [
            // -----------------------------------------------------------------
            // FULL-TIME  x  salary < 10,000
            // -----------------------------------------------------------------
            [
                'temp_id'       => 'fh-001',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Chai Corner',
                'job_role'      => 'Tea Maker',
                'job_type'      => 'Full-time',
                'salary'        => 8000,
                'expires_at'    => now()->addHours(20),   // within a day
            ],
            [
                'temp_id'       => 'fh-002',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Biryani House',
                'job_role'      => 'Kitchen Helper',
                'job_type'      => 'Full-time',
                'salary'        => 7500,
                'expires_at'    => now()->addDays(2),     // within 3 days
            ],
            [
                'temp_id'       => 'fh-003',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Roti Palace',
                'job_role'      => 'Dishwasher',
                'job_type'      => 'Full-time',
                'salary'        => 9000,
                'expires_at'    => now()->addDays(5),     // within a week
            ],
            [
                'temp_id'       => 'fh-004',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Dhaba Express',
                'job_role'      => 'Server',
                'job_type'      => 'Full-time',
                'salary'        => 8500,
                'expires_at'    => now()->addDays(15),    // beyond a week
            ],

            // -----------------------------------------------------------------
            // FULL-TIME  x  salary < 20,000  (10001 - 19999)
            // -----------------------------------------------------------------
            [
                'temp_id'       => 'fh-005',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Hotel Sunrise',
                'job_role'      => 'Waiter',
                'job_type'      => 'Full-time',
                'salary'        => 15000,
                'expires_at'    => now()->addHours(10),   // within a day
            ],
            [
                'temp_id'       => 'fh-006',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Pizza Point',
                'job_role'      => 'Delivery Boy',
                'job_type'      => 'Full-time',
                'salary'        => 12000,
                'expires_at'    => now()->addDays(2),     // within 3 days
            ],
            [
                'temp_id'       => 'fh-007',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Cake Studio',
                'job_role'      => 'Baker',
                'job_type'      => 'Full-time',
                'salary'        => 18000,
                'expires_at'    => now()->addDays(6),     // within a week
            ],

            // -----------------------------------------------------------------
            // FULL-TIME  x  salary 21,000 and above
            // -----------------------------------------------------------------
            [
                'temp_id'       => 'fh-008',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Grand Banquet Hall',
                'job_role'      => 'Head Chef',
                'job_type'      => 'Full-time',
                'salary'        => 45000,
                'expires_at'    => now()->addHours(18),   // within a day
            ],
            [
                'temp_id'       => 'fh-009',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Royal Caterers',
                'job_role'      => 'Sous Chef',
                'job_type'      => 'Full-time',
                'salary'        => 30000,
                'expires_at'    => now()->addDays(3),     // within 3 days (boundary)
            ],
            [
                'temp_id'       => 'fh-010',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Taj Restaurant',
                'job_role'      => 'Restaurant Manager',
                'job_type'      => 'Full-time',
                'salary'        => 55000,
                'expires_at'    => now()->addDays(5),     // within a week
            ],

            // -----------------------------------------------------------------
            // PART-TIME  x  salary < 10,000
            // -----------------------------------------------------------------
            [
                'temp_id'       => 'fh-011',
                'subcategory'   => 'Food Service',             // variant - matches "food"
                'business_name' => 'Momos Point',
                'job_role'      => 'Counter Staff',
                'job_type'      => 'Part-Time',
                'salary'        => 6000,
                'expires_at'    => now()->addHours(22),    // within a day
            ],
            [
                'temp_id'       => 'fh-012',
                'subcategory'   => 'Food Service',
                'business_name' => 'Cold Drink Stall',
                'job_role'      => 'Juice Maker',
                'job_type'      => 'Part-Time',
                'salary'        => 5000,
                'expires_at'    => now()->addDays(1),      // within 3 days
            ],
            [
                'temp_id'       => 'fh-013',
                'subcategory'   => 'Hospitality Services',     // variant - matches "hospitality"
                'business_name' => 'Budget Hotel',
                'job_role'      => 'Housekeeper',
                'job_type'      => 'Part-Time',
                'salary'        => 7000,
                'expires_at'    => now()->addDays(4),      // within a week
            ],

            // -----------------------------------------------------------------
            // PART-TIME  x  salary 21,000 and above
            // -----------------------------------------------------------------
            [
                'temp_id'       => 'fh-014',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Event Catering Co.',
                'job_role'      => 'Senior Bartender',
                'job_type'      => 'Part-Time',
                'salary'        => 25000,
                'expires_at'    => now()->addHours(16),    // within a day
            ],
            [
                'temp_id'       => 'fh-015',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Fine Dine City',
                'job_role'      => 'Sommelier',
                'job_type'      => 'Part-Time',
                'salary'        => 35000,
                'expires_at'    => now()->addDays(6),      // within a week
            ],

            // -----------------------------------------------------------------
            // TEMPORARY  x  all salary ranges
            // -----------------------------------------------------------------
            [
                'temp_id'       => 'fh-016',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Festival Stall',
                'job_role'      => 'Snack Seller',
                'job_type'      => 'Temporary',
                'salary'        => 4000,
                'expires_at'    => now()->addHours(23),    // within a day
            ],
            [
                'temp_id'       => 'fh-017',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Wedding Caterers',
                'job_role'      => 'Banquet Server',
                'job_type'      => 'Temporary',
                'salary'        => 14000,
                'expires_at'    => now()->addDays(2),      // within 3 days
            ],
            [
                'temp_id'       => 'fh-018',
                'subcategory'   => 'Food and Hospitality',
                'business_name' => 'Cruise Kitchen',
                'job_role'      => 'Chef de Partie',
                'job_type'      => 'Temporary',
                'salary'        => 28000,
                'expires_at'    => now()->addDays(5),      // within a week
            ],

            // -----------------------------------------------------------------
            // CONTROL: Different subcategory - should NOT match Food/Hospitality
            // -----------------------------------------------------------------
            [
                'temp_id'       => 'fh-ctrl-01',
                'subcategory'   => 'IT and Software',
                'business_name' => 'Tech Startup',
                'job_role'      => 'Developer',
                'job_type'      => 'Full-time',
                'salary'        => 50000,
                'expires_at'    => now()->addDays(5),
            ],
            [
                'temp_id'       => 'fh-ctrl-02',
                'subcategory'   => 'Retail and Sales',
                'business_name' => 'Big Mart',
                'job_role'      => 'Sales Executive',
                'job_type'      => 'Part-Time',
                'salary'        => 12000,
                'expires_at'    => now()->addDays(2),
            ],
        ];

        foreach ($jobs as $job) {
            Job::firstOrCreate(
                ['temp_id' => $job['temp_id']],
                array_merge($base, $job)
            );
        }

        $this->command->info('FoodHospitalityJobSeeder: ' . count($jobs) . ' jobs seeded.');
    }
}
