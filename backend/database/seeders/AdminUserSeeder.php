<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::getByName(Role::ADMIN);
        $garantRole = Role::getByName(Role::GARANT);
        $studentRole = Role::getByName(Role::STUDENT);
        $companyRole = Role::getByName(Role::COMPANY);

        // Create admin user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
            ]
        );

        // Create garant user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'garant@test.com'],
            [
                'name' => 'Garant User',
                'password' => Hash::make('password123'),
                'role_id' => $garantRole->id,
            ]
        );

        // Create student user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'student@test.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('password123'),
                'role_id' => $studentRole->id,
            ]
        );

        // Create company user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'company@test.com'],
            [
                'name' => 'Company User',
                'password' => Hash::make('password123'),
                'role_id' => $companyRole->id,
            ]
        );

        // Create additional test users for comprehensive testing
        User::firstOrCreate(
            ['email' => 'admin2@test.com'],
            [
                'name' => 'Admin User 2',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'garant2@test.com'],
            [
                'name' => 'Garant User 2',
                'password' => Hash::make('password123'),
                'role_id' => $garantRole->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'company2@test.com'],
            [
                'name' => 'Company User 2',
                'password' => Hash::make('password123'),
                'role_id' => $companyRole->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'student2@test.com'],
            [
                'name' => 'Student User 2',
                'password' => Hash::make('password123'),
                'role_id' => $studentRole->id,
            ]
        );
    }
}