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

        Student::create([
            'name' => 'Peter',
            'surname' => 'Hudec',
            'student_email' => 'peter.hudec@ukf.sk',
            'alternative_email' => 'hudec.peter@gmail.com',
            'phone_number' => '+421 900 123 456',
            'user_id' => 1, // ak máš už vytvoreného používateľa v `users` tabuľke
            'study_level' => 'Bc.',
            'city' => 'Nitra',
            'street' => 'Hlavná',
            'house_number' => '15A',
        ]);
    }
}
