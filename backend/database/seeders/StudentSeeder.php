<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a student user
        $studentUser = User::where('email', 'student@test.com')->first();
        
        if (!$studentUser) {
            return; // Skip if user doesn't exist yet
        }

        Student::firstOrCreate(
            ['student_email' => 'peter.hudec@ukf.sk'],
            [
                'name' => 'Peter',
                'surname' => 'Hudec',
                'alternative_email' => 'hudec.peter@gmail.com',
                'phone_number' => '+421 900 123 456',
                'user_id' => $studentUser->id,
                'study_level' => 'Bc.',
                'state' => 'Slovensko',
                'region' => 'Nitriansky kraj',
                'city' => 'Nitra',
                'postal_code' => '94901',
                'street' => 'Hlavná',
                'house_number' => '15A',
            ]
        );
    }
}
