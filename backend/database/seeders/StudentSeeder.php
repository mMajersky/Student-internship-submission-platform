<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student; // Uistite sa, že model Student existuje na tejto ceste

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vytvorenie prvého študenta
        Student::create([
            'name' => 'Peter',
            'surname' => 'Hudec',
            'student_email' => 'peter.hudec@ukf.sk',
            'alternative_email' => 'hudec.peter@gmail.com',
            'phone_number' => '+421 900 123 456',
            // DÔLEŽITÉ: Uistite sa, že používateľ s id=2 existuje v tabuľke `users`
            // a má rolu 'student'. Predpokladám, že id=1 má admin.
            'user_id' => 2,
            'study_level' => 'Bc.',
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '949 01',
            'street' => 'Hlavná',
            'house_number' => '15A',
        ]);
    }
}