<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['mobile' => '0501111111'],
            ['mobile' => '0502222222'],
            ['mobile' => '0503333333'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['mobile' => $customer['mobile']],
                []
            );
        }
    }
}
