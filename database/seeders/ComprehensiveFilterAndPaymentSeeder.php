<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Job;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ComprehensiveFilterAndPaymentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Plans
        $planBasic = Plan::firstOrCreate(
            ['plan_title' => 'Basic'],
            ['duration' => '30 days', 'price' => 499]
        );
        $planPro = Plan::firstOrCreate(
            ['plan_title' => 'Pro'],
            ['duration' => '90 days', 'price' => 1299]
        );

        // 2. Customers & Credits
        $customer1 = Customer::firstOrCreate(
            ['mobile' => '9876543210'],
            ['customer_id' => 'PSTGL_TEST_01', 'fcm' => 'fcm_token_test_1']
        );
        CustomerCredit::firstOrCreate(
            ['customer_id' => $customer1->customer_id],
            ['balance' => 1500.00]
        );

        $customer2 = Customer::firstOrCreate(
            ['mobile' => '9876543211'],
            ['customer_id' => 'PSTGL_TEST_02', 'fcm' => 'fcm_token_test_2']
        );
        CustomerCredit::firstOrCreate(
            ['customer_id' => $customer2->customer_id],
            ['balance' => 500.00]
        );

        // 3. Seed Jobs with various filters (Location: Noida 28.5914, 77.4021)
        $now = Carbon::now();

        $jobConfigs = [
            [
                'temp_id' => 'job-seed-food-fulltime-low',
                'device_id' => 'dev-001',
                'device_os' => 'android',
                'master_category' => 'JOB',
                'subcategory' => 'Food and Hospitality',
                'business_name' => 'Tasty Burger Corner',
                'job_role' => 'Burger Chef',
                'job_type' => 'full_time',
                'salary' => 8000,
                'phone_number' => '9876543210',
                'latitude' => 28.5914,
                'longitude' => 77.4021,
                'city' => 'Noida',
                'status' => 'approved',
                'approved_at' => $now,
                'expires_at' => $now->copy()->addDays(1),
                'plan_id' => (string) $planBasic->id,
            ],
            [
                'temp_id' => 'job-seed-hospitality-parttime-mid',
                'device_id' => 'dev-002',
                'device_os' => 'ios',
                'master_category' => 'JOB',
                'subcategory' => 'Hospitality Services',
                'business_name' => 'Grand Hotel Noida',
                'job_role' => 'Receptionist',
                'job_type' => 'part_time',
                'salary' => 15000,
                'phone_number' => '9876543210',
                'latitude' => 28.5914,
                'longitude' => 77.4021,
                'city' => 'Noida',
                'status' => 'approved',
                'approved_at' => $now,
                'expires_at' => $now->copy()->addDays(3),
                'plan_id' => (string) $planBasic->id,
            ],
            [
                'temp_id' => 'job-seed-it-temp-high',
                'device_id' => 'dev-003',
                'device_os' => 'android',
                'master_category' => 'JOB',
                'subcategory' => 'IT and Software',
                'business_name' => 'NextGen Cloud Solutions',
                'job_role' => 'Backend Engineer',
                'job_type' => 'temporary',
                'salary' => 35000,
                'phone_number' => '9876543211',
                'latitude' => 28.5914,
                'longitude' => 77.4021,
                'city' => 'Noida',
                'status' => 'approved',
                'approved_at' => $now,
                'expires_at' => $now->copy()->addDays(7),
                'plan_id' => (string) $planPro->id,
            ],
            [
                'temp_id' => 'job-seed-shop-fulltime-high',
                'device_id' => 'dev-004',
                'device_os' => 'android',
                'master_category' => 'JOB',
                'subcategory' => 'Shop/Office/School Staff',
                'business_name' => 'Modern Book Depot',
                'job_role' => 'Store Manager',
                'job_type' => 'full_time',
                'salary' => 22000,
                'phone_number' => '9876543212',
                'latitude' => 28.5914,
                'longitude' => 77.4021,
                'city' => 'Noida',
                'status' => 'approved',
                'approved_at' => $now,
                'expires_at' => $now->copy()->addDays(20),
                'plan_id' => (string) $planBasic->id,
            ],
            [
                'temp_id' => 'job-seed-healthcare-far',
                'device_id' => 'dev-005',
                'device_os' => 'ios',
                'master_category' => 'JOB',
                'subcategory' => 'Healthcare',
                'business_name' => 'City Care Clinic',
                'job_role' => 'Nurse',
                'job_type' => 'full_time',
                'salary' => 18000,
                'phone_number' => '9876543213',
                'latitude' => 28.8000,
                'longitude' => 77.8000, // Outside 10km radius
                'city' => 'Greater Noida',
                'status' => 'approved',
                'approved_at' => $now,
                'expires_at' => $now->copy()->addDays(5),
                'plan_id' => (string) $planBasic->id,
            ],
        ];

        foreach ($jobConfigs as $config) {
            Job::firstOrCreate(['temp_id' => $config['temp_id']], $config);
        }

        // 4. Seed Offers with various filters
        $offerConfigs = [
            [
                'temp_id' => 'offer-seed-food-discount',
                'device_id' => 'dev-001',
                'device_os' => 'android',
                'master_category' => 'OFFER',
                'subcategory' => 'Food and Hospitality',
                'business_name' => 'Pizza House',
                'offer_details' => 'Flat 50% off on large pizzas',
                'offer_type' => 'discount',
                'amount' => 499,
                'mobile_number' => '9876543210',
                'latitude' => 28.5914,
                'longitude' => 77.4021,
                'city' => 'Noida',
                'status' => 'approved',
                'approved_at' => $now,
                'expires_at' => $now->copy()->addDays(1),
                'plan_id' => (string) $planBasic->id,
            ],
            [
                'temp_id' => 'offer-seed-shop-combo',
                'device_id' => 'dev-002',
                'device_os' => 'ios',
                'master_category' => 'OFFER',
                'subcategory' => 'Shop/Office/School Staff',
                'business_name' => 'Kids Stationery Mall',
                'offer_details' => 'Buy 2 notebooks get 1 pen free',
                'offer_type' => 'combo',
                'amount' => 299,
                'mobile_number' => '9876543210',
                'latitude' => 28.5914,
                'longitude' => 77.4021,
                'city' => 'Noida',
                'status' => 'approved',
                'approved_at' => $now,
                'expires_at' => $now->copy()->addDays(3),
                'plan_id' => (string) $planBasic->id,
            ],
            [
                'temp_id' => 'offer-seed-it-delivery',
                'device_id' => 'dev-003',
                'device_os' => 'android',
                'master_category' => 'OFFER',
                'subcategory' => 'IT and Software',
                'business_name' => 'Computer Repair Hub',
                'offer_details' => 'Free doorstep pickup & delivery',
                'offer_type' => 'delivery',
                'amount' => 799,
                'mobile_number' => '9876543211',
                'latitude' => 28.5914,
                'longitude' => 77.4021,
                'city' => 'Noida',
                'status' => 'approved',
                'approved_at' => $now,
                'expires_at' => $now->copy()->addDays(7),
                'plan_id' => (string) $planPro->id,
            ],
        ];

        foreach ($offerConfigs as $config) {
            Offer::firstOrCreate(['temp_id' => $config['temp_id']], $config);
        }

        // 5. Seed Payments covering FULL_UPI, SEMI, FULL_CREDIT
        $paymentConfigs = [
            [
                'transaction_id' => 'TXN_SEEDED_FULL_UPI_001',
                'item_type' => 'job',
                'job_or_offer_id' => 1,
                'payment_type' => 'FULL_UPI',
                'total_amount' => 499.00,
                'razorpay_amount' => 499.00,
                'credit_amount' => 0.00,
                'razorpay_order_id' => 'order_upi_12345',
                'razorpay_payment_id' => 'pay_upi_12345',
                'payment_status' => 'COMPLETED',
                'credit_mode' => 'full_upi',
                'amount' => 499.00,
            ],
            [
                'transaction_id' => 'TXN_SEEDED_SEMI_002',
                'item_type' => 'job',
                'job_or_offer_id' => 2,
                'payment_type' => 'SEMI',
                'total_amount' => 1299.00,
                'razorpay_amount' => 799.00,
                'credit_amount' => 500.00,
                'razorpay_order_id' => 'order_semi_67890',
                'razorpay_payment_id' => 'pay_semi_67890',
                'payment_status' => 'COMPLETED',
                'credit_mode' => 'semi',
                'amount' => 1299.00,
            ],
            [
                'transaction_id' => 'TXN_SEEDED_FULL_CREDIT_003',
                'item_type' => 'offer',
                'job_or_offer_id' => 1,
                'payment_type' => 'FULL_CREDIT',
                'total_amount' => 299.00,
                'razorpay_amount' => 0.00,
                'credit_amount' => 299.00,
                'razorpay_order_id' => null,
                'razorpay_payment_id' => null,
                'payment_status' => 'COMPLETED',
                'credit_mode' => 'full_credit',
                'amount' => 299.00,
            ],
        ];

        foreach ($paymentConfigs as $config) {
            Payment::firstOrCreate(['transaction_id' => $config['transaction_id']], $config);
        }
    }
}
