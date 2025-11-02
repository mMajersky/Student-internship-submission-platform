<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'student_email' => $this->faker->unique()->safeEmail(),
            'alternative_email' => $this->faker->optional()->safeEmail(),
            'phone_number' => $this->faker->optional()->phoneNumber(),
            'user_id' => User::factory(),
            'study_level' => $this->faker->randomElement(['Bc.', 'Ing.', 'PhD.']),
            'state' => $this->faker->country(),
            'region' => $this->faker->state(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'street' => $this->faker->streetName(),
            'house_number' => $this->faker->buildingNumber(),
        ];
    }
}
