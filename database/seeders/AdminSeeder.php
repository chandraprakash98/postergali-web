<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );
    }
}
