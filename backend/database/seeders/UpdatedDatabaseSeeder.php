<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Garant;

class UpdatedDatabaseSeeder extends Seeder
{
    public function run(): void
    {

        // Garant používateľ
        $garant = User::create([
            'name' => 'Garant',
            'email' => 'garant@sisp.sk',
            'password' => Hash::make('garant123'),
            'role' => 'garant',
        ]);

        // Študent používateľ
        $student = User::create([
            'name' => 'Študent',
            'email' => 'student@sisp.sk',
            'password' => Hash::make('student123'),
            'role' => 'student',
        ]);

        // Záznam v tabuľke students
        Student::create([
            'name' => 'Ján',
            'surname' => 'Kováč',
            'student_email' => 'jan.kovac@sisp.sk',
            'user_id' => $student->id,
            'study_level' => 'Bc.',
            'city' => 'Nitra',
            'street' => 'Hlavná',
            'house_number' => '15A',
        ]);


        // Záznam v tabuľke garants
        Garant::create([
            'name' => 'Garant',
            'surname' => 'Kováč',
            'faculty' => 'FPV',
            'user_id' => $garant->id,
        ]);
    }
}
