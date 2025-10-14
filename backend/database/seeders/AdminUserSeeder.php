<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Create garant user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'garant@test.com'],
            [
                'name' => 'Garant User',
                'password' => Hash::make('password123'),
                'role' => 'garant',
            ]
        );

        // Create student user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'student@test.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('password123'),
                'role' => 'student',
            ]
        );
    }
}