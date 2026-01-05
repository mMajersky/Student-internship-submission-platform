<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ContactPerson;

class ContactPersonFactory extends Factory
{
    protected $model = ContactPerson::class;

    public function definition(): array
    {
        return [
            'company_id' => null,
            'name' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'email' => $this->faker->unique()->companyEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'position' => $this->faker->jobTitle(),
        ];
    }
}
