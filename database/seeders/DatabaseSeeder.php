<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Seed admin user
        $this->call(AdminSeeder::class);
        
        // Seed sample data
        $this->call(SampleDataSeeder::class);

        // Seed customer records
        $this->call(CustomerSeeder::class);

        // Seed comprehensive filter & payment data
        $this->call(ComprehensiveFilterAndPaymentSeeder::class);
    }
}
