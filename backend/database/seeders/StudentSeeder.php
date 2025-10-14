<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Address;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1️⃣ Najprv vytvoríme adresu
        $address = Address::create([
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Trieda A. Hlinku',
            'house_number' => '1',
        ]);

        // 2️⃣ Potom vytvoríme študenta
        Student::create([
            'name' => 'Peter',
            'surname' => 'Hudec',
            'student_email' => 'peter.hudec@ukf.sk',
            'alternative_email' => 'hudec.peter@gmail.com',
            'address_id' => $address->id,
            'phone_number' => '+421 900 123 456',
            'user_id' => 1, // ak máš už vytvoreného používateľa v `users` tabuľke
        ]);
    }
}
