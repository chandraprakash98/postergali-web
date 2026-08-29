<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\Offer;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample plans
        $basicPlan = Plan::firstOrCreate(
            ['plan_title' => 'Basic'],
            [
                'duration' => '30 days',
                'price' => 499,
            ]
        );

        $proPlan = Plan::firstOrCreate(
            ['plan_title' => 'Pro'],
            [
                'duration' => '90 days',
                'price' => 1299,
            ]
        );

        $premiumPlan = Plan::firstOrCreate(
            ['plan_title' => 'Premium'],
            [
                'duration' => '180 days',
                'price' => 2499,
            ]
        );

        // Create sample jobs
        $jobData = [
            [
                'business_name' => 'Tech Solutions Inc',
                'phone_number' => '+91 9210403857',
                'city' => 'Pune',
                'status' => 'pending',
                'job_role' => 'Software Engineer',
                'job_type' => 'Full-time',
            ],
            [
                'business_name' => 'Green Garden Cafe',
                'phone_number' => '+91 9210403857',
                'city' => 'Mumbai',
                'status' => 'pending',
                'job_role' => 'Chef',
                'job_type' => 'Full-time',
            ],
            [
                'business_name' => 'Fitness First Gym',
                'phone_number' => '+91 9210403857',
                'city' => 'Pune',
                'status' => 'pending',
                'job_role' => 'Trainer',
                'job_type' => 'Part-time',
            ],
            [
                'business_name' => 'Urban Fashion Boutique',
                'phone_number' => '+91 9210403857',
                'city' => 'Nashik',
                'status' => 'approved',
                'job_role' => 'Sales Associate',
                'job_type' => 'Full-time',
            ],
            [
                'business_name' => 'Smart Home Services',
                'phone_number' => '+91 9210403857',
                'city' => 'New Delhi',
                'status' => 'approved',
                'job_role' => 'Technician',
                'job_type' => 'Full-time',
            ],
            [
                'business_name' => 'Pet Paradise Grooming',
                'phone_number' => '+91 9210403857',
                'city' => 'Pune',
                'status' => 'rejected',
                'job_role' => 'Groomer',
                'job_type' => 'Full-time',
            ],
            [
                'business_name' => 'Sunset Real Estate',
                'phone_number' => '+91 9210403857',
                'city' => 'Mumbai',
                'status' => 'approved',
                'job_role' => 'Agent',
                'job_type' => 'Full-time',
            ],
            [
                'business_name' => 'Elite Auto Repair',
                'phone_number' => '+91 9210403857',
                'city' => 'Pune',
                'status' => 'rejected',
                'job_role' => 'Mechanic',
                'job_type' => 'Full-time',
            ],
            [
                'business_name' => 'Bright Minds Tutoring',
                'phone_number' => '+91 9210403857',
                'city' => 'Nashik',
                'status' => 'pending',
                'job_role' => 'Tutor',
                'job_type' => 'Part-time',
            ],
        ];

        foreach ($jobData as $data) {
            $data['temp_id'] = 'sample-job-' . uniqid();
            $data['device_id'] = 'sample-device-001';
            $data['device_os'] = 'android';
            $data['master_category'] = 'JOB';
            $data['subcategory'] = 'General';
            $data['latitude'] = '28.8889';
            $data['longitude'] = '77.2088';
            $data['plan_id'] = (string) $basicPlan->id;
            $data['expires_at'] = now()->addDays(30);
            $data['approved_at'] = $data['status'] === 'approved' ? now() : null;

            Job::firstOrCreate(
                ['business_name' => $data['business_name'], 'city' => $data['city']],
                $data
            );
        }

        // Create sample offers
        $offerData = [
            [
                'business_name' => 'Pizza Palace',
                'mobile_number' => '+91 9210403857',
                'city' => 'Pune',
                'status' => 'pending',
                'offer_details' => '50% off on all pizzas',
                'offer_type' => 'Discount',
            ],
            [
                'business_name' => 'Beauty Paradise Salon',
                'mobile_number' => '+91 9210403857',
                'city' => 'Mumbai',
                'status' => 'pending',
                'offer_details' => 'Free haircut with color service',
                'offer_type' => 'Combo',
            ],
            [
                'business_name' => 'Electronics Hub',
                'mobile_number' => '+91 9210403857',
                'city' => 'Pune',
                'status' => 'approved',
                'offer_details' => 'Free delivery on orders above 2000',
                'offer_type' => 'Delivery',
            ],
            [
                'business_name' => 'Clothing Outlet',
                'mobile_number' => '+91 9210403857',
                'city' => 'Nashik',
                'status' => 'approved',
                'offer_details' => 'Buy 2 Get 1 Free',
                'offer_type' => 'Promotion',
            ],
            [
                'business_name' => 'Restaurant XYZ',
                'mobile_number' => '+91 9210403857',
                'city' => 'New Delhi',
                'status' => 'rejected',
                'offer_details' => 'Limited time offer - expired',
                'offer_type' => 'Discount',
            ],
        ];

        foreach ($offerData as $data) {
            $data['temp_id'] = 'sample-offer-' . uniqid();
            $data['device_id'] = 'sample-device-001';
            $data['device_os'] = 'android';
            $data['master_category'] = 'OFFER';
            $data['subcategory'] = 'General';
            $data['latitude'] = '28.8889';
            $data['longitude'] = '77.2088';
            $data['plan_id'] = (string) $basicPlan->id;
            $data['expires_at'] = now()->addDays(30);
            $data['approved_at'] = $data['status'] === 'approved' ? now() : null;

            Offer::firstOrCreate(
                ['business_name' => $data['business_name'], 'city' => $data['city']],
                $data
            );
        }
    }
}
