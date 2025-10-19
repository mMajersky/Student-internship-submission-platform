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
                'name' => 'Admin',
                'surname' => 'User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Create garant user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'garant@test.com'],
            [
                'name' => 'Garant',
                'surname' => 'User',
                'password' => Hash::make('password123'),
                'role' => 'garant',
            ]
        );

        // Create student user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'student@test.com'],
            [
                'name' => 'Student',
                'surname' => 'User',
                'password' => Hash::make('password123'),
                'role' => 'student',
            ]
        );

        // Create company user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'company@test.com'],
            [
                'name' => 'Company',
                'surname' => 'User',
                'password' => Hash::make('password123'),
                'role' => 'company',
            ]
        );

        // Create additional test users for comprehensive testing
        User::firstOrCreate(
            ['email' => 'admin2@test.com'],
            [
                'name' => 'Admin',
                'surname' => 'User 2',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'garant2@test.com'],
            [
                'name' => 'Garant',
                'surname' => 'User 2',
                'password' => Hash::make('password123'),
                'role' => 'garant',
            ]
        );

        User::firstOrCreate(
            ['email' => 'company2@test.com'],
            [
                'name' => 'Company',
                'surname' => 'User 2',
                'password' => Hash::make('password123'),
                'role' => 'company',
            ]
        );

        User::firstOrCreate(
            ['email' => 'student2@test.com'],
            [
                'name' => 'Student',
                'surname' => 'User 2',
                'password' => Hash::make('password123'),
                'role' => 'student',
            ]
        );
    }
}