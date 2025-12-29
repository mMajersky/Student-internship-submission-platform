<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'surname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement([
                'student',
                'company',
                'garant'
            ]),
        ];
    }

    public function student(): static
    {
        return $this->state(fn () => ['role' => 'student']);
    }

    public function company(): static
    {
        return $this->state(fn () => ['role' => 'company']);
    }

    public function garant(): static
    {
        return $this->state(fn () => ['role' => 'garant']);
    }
}
