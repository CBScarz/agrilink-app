<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@agrilink.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123456'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create Test Farmer
        $farmer = User::create([
            'name' => 'John Farmer',
            'email' => 'farmer@agrilink.com',
            'email_verified_at' => now(),
            'password' => Hash::make('farmer123456'),
            'role' => 'farmer',
            'status' => 'active',
        ]);

        // Create Test Buyer
        User::create([
            'name' => 'Jane Buyer',
            'email' => 'buyer@agrilink.com',
            'email_verified_at' => now(),
            'password' => Hash::make('buyer123456'),
            'role' => 'buyer',
            'status' => 'active',
        ]);
    }
}
