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
        // Vytvorí testovacích používateľov
        $studentUser = User::create([
            'email' => 'test@example.com',
            'role' => 'student',
            'pwd' => Hash::make('password'),
        ]);

        $companyUser = User::create([
            'email' => 'company@example.com',
            'role' => 'company',
            'pwd' => Hash::make('admin'),
        ]);
        $this->call([
            StudentSeeder::class,
        ]);

        // Vytvoríme adresu
        $address = Address::create([
            'state' => 'Slovensko',
            'region' => 'Bratislavský kraj',
            'city' => 'Bratislava',
            'postal_code' => '81101',
            'street' => 'Vazovova',
            'house_number' => '10',
        ]);

        // Vytvoríme firmu
        Company::create([
            'name' => 'Example Company s.r.o.',
            'statutary' => 'Ing. Jozef Mrkvička',
            'address_id' => $address->id,
            'user_id' => $companyUser->id,
        ]);
    }
}