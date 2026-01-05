<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Student;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'name' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'student_email' => $this->faker->unique()->safeEmail(),
            'alternative_email' => $this->faker->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'study_level' => 'Bc.',
            'study_field' => 'Informatika',
            'state' => 'Slovensko',
            'region' => $this->faker->randomElement(['Nitriansky', 'Bratislavský', 'Trenčiansky']),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'street' => $this->faker->streetName(),
            'house_number' => $this->faker->buildingNumber(),
        ];
    }
}
