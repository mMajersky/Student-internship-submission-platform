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
            ->count(40) // 🔥 viac študentov
            ->create(['role' => 'student'])
            ->each(function ($user) use ($students) {
                $student = Student::factory()->create([
                    'user_id' => $user->id
                ]);
                $students->push($student);
            });

        // -------------------------------------------------------------
        // 3. Internship configuration (UNIFIED WITH MODEL)
        // -------------------------------------------------------------
        $types = [
            Internship::TYPE_PAID,
            Internship::TYPE_UNPAID,
            Internship::TYPE_SCHOOL_PROJECT,
        ];

        // Vážené rozdelenie statusov (realistické)
        $statusWeights = [
            Internship::STATUS_CREATED         => 10,
            Internship::STATUS_APPROVED        => 20,
            Internship::STATUS_REJECTED        => 5,
            Internship::STATUS_CONFIRMED       => 25,
            Internship::STATUS_NOT_CONFIRMED   => 5,
            Internship::STATUS_DEFENDED        => 25,
            Internship::STATUS_NOT_DEFENDED    => 10,
        ];

        // -------------------------------------------------------------
        // 4. Generate internships
        // -------------------------------------------------------------
        for ($i = 0; $i < 120; $i++) { // 🔥 výrazne viac dát
            Internship::factory()->create([
                'student_id' => $students->random()->id,
                'company_id' => $companies->random()->id,
                'garant_id'  => 1, // ak máš len jedného garanta
                'type'       => collect($types)->random(),
                'status'     => $this->weightedRandom($statusWeights),
                'academy_year' => collect([
                    '2023/2024',
                    '2024/2025',
                    '2025/2026',
                ])->random(),
            ]);
        }
    }

    /**
     * Pick a value using weighted random distribution
     */
    private function weightedRandom(array $weights): string
    {
        $sum = array_sum($weights);
        $rand = rand(1, $sum);

        foreach ($weights as $value => $weight) {
            $rand -= $weight;
            if ($rand <= 0) {
                return $value;
            }
        }

        // fallback (should never happen)
        return array_key_first($weights);
    }
}
