<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Student;
use App\Models\Company;
use App\Models\Garant;
use App\Models\Internship;

class InternshipFactory extends Factory
{
    protected $model = Internship::class;

    public function definition()
    {
        return [
            'student_id' => \App\Models\Student::factory(),
            'company_id' => \App\Models\Company::factory(),
            'garant_id' => 1, // alebo Garant::factory()
            'status' => 'Created',
            'academy_year' => '2024/2025',
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'confirmed_date' => null,
            'approved_date' => null,
            'type' => 'unpaid',
        ];
    }
}
