<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['mobile' => '0501111111', 'customer_id' => 'PSTGL00001'],
            ['mobile' => '0502222222', 'customer_id' => 'PSTGL00002'],
            ['mobile' => '0503333333', 'customer_id' => 'PSTGL00003'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['mobile' => $customer['mobile']],
                ['customer_id' => $customer['customer_id']]
            );
        }
    }
}
