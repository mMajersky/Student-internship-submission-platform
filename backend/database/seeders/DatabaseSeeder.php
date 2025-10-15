<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders from develop
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            StudentSeeder::class,
            ContactPersonSeeder::class,
            InternshipSeeder::class,
        ]);

        // Create a company user for testing purposes
        $companyUser = User::create([
            'email' => 'company@example.com',
            'role' => 'company',
            'pwd' => Hash::make('admin'),
        ]);

        // Create a student user for testing purposes
        $studentUser = User::create([
            'email' => 'test@example.com',
            'role' => 'student',
            'pwd' => Hash::make('password'),
        ]);

        // Create an address
        $address = Address::create([
            'state' => 'Slovensko',
            'region' => 'Bratislavský kraj',
            'city' => 'Bratislava',
            'postal_code' => '81101',
            'street' => 'Vazovova',
            'house_number' => '10',
        ]);

        // Create a company
        Company::create([
            'name' => 'Example Company s.r.o.',
            'statutary' => 'Ing. Jozef Mrkvička',
            'address_id' => $address->id,
            'user_id' => $companyUser->id,
        ]);
    }
}