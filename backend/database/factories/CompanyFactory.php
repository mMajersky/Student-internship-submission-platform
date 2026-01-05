<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'user_id' => null, // môžeme neskôr doplniť ak chceš mať usera pre firmu
            'name' => $this->faker->company(),
            'state' => 'Slovensko',
            'region' => $this->faker->randomElement(['Bratislavský', 'Trnavský', 'Nitriansky']),
            'city' => $this->faker->city(),
            'street' => $this->faker->streetName(),
            'house_number' => $this->faker->buildingNumber(),
            'postal_code' => $this->faker->postcode(),
        ];
    }
}
