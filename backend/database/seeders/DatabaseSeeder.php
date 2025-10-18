<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Call seeders in correct order
        $this->call([
            AdminUserSeeder::class,
        ]);

        // Create a company user for testing purposes
        $companyUser = User::firstOrCreate(
            ['email' => 'company@example.com'],
            [
                'name' => 'Company',
                'surname' => 'User',
                'role' => 'company',
                'password' => Hash::make('admin'),
            ]
        );

        // Create a company with address fields directly
        Company::firstOrCreate(
            ['name' => 'Example Company s.r.o.'],
            [
                'user_id' => $companyUser->id,
                'state' => 'Slovensko',
                'region' => 'Bratislavský kraj',
                'city' => 'Bratislava',
                'postal_code' => '81101',
                'street' => 'Vazovova',
                'house_number' => '10',
            ]
        );

        // Call other seeders that depend on users and companies
        $this->call([
            StudentSeeder::class,
            ContactPersonSeeder::class,
            InternshipSeeder::class,
        ]);
    }
}
