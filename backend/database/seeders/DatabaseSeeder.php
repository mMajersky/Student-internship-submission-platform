<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use App\Models\Company;
use App\Models\Role;
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
            RoleSeeder::class,
            AdminUserSeeder::class,
            StudentSeeder::class,
            ContactPersonSeeder::class,
            InternshipSeeder::class,
        ]);

        // Create a company user for testing purposes
        $companyRole = Role::where('name', Role::COMPANY)->first();
        $companyUser = User::create([
            'name' => 'Company User',
            'email' => 'company@example.com',
            'role_id' => $companyRole ? $companyRole->id : null,
            'password' => Hash::make('admin'),
        ]);

        // Create a student user for testing purposes
        $studentRole = Role::where('name', Role::STUDENT)->first();
        $studentUser = User::create([
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'role_id' => $studentRole ? $studentRole->id : null,
            'password' => Hash::make('password'),
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
