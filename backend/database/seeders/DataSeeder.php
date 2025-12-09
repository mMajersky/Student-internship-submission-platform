<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Student;
use App\Models\Internship;

class DataSeeder extends Seeder
{
    public function run()
    {
        // -------------------------------------------------------------
        // 1. Generate companies
        // -------------------------------------------------------------
        $companies = Company::factory()->count(15)->create();

        // -------------------------------------------------------------
        // 2. Generate students (user + student record)
        // -------------------------------------------------------------
        $students = collect();

        User::factory()
            ->count(30)
            ->create(['role' => 'student'])
            ->each(function ($user) use ($students) {
                $student = Student::factory()->create([
                    'user_id' => $user->id
                ]);
                $students->push($student);
            });

        // -------------------------------------------------------------
        // 3. Internship configuration
        // -------------------------------------------------------------
        $types = ['unpaid', 'paid', 'school_project'];

        $statuses = [
            'Created',
            'Garant_Approved',
            'Garant_Rejected',
            'Company_Approved',
            'Company_Rejected',
            'Defended',
            'Not_Defended',
        ];

        // -------------------------------------------------------------
        // 4. Generate internships
        // -------------------------------------------------------------
        Internship::factory()
            ->count(50)
            ->create([
                'student_id' => fn () => $students->random()->id,
                'company_id' => fn () => $companies->random()->id,
                'garant_id'  => 1, // alebo random garant ak budeš mať seedovaných viacerých
                'type'       => fn () => collect($types)->random(),
                'status'     => fn () => collect($statuses)->random(),
            ]);
    }
}
