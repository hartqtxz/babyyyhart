<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'user',
        ];
    }

    /**
     * Create an admin user with @portal.com email
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => 'admin' . fake()->unique()->numberBetween(1, 9999) . '@portal.com',
            'role' => 'admin',
        ]);
    }

    /**
     * Create a regular user with @gmail.com email
     */
    public function gmailUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => fake()->unique()->firstName() . fake()->unique()->numberBetween(100, 9999) . '@gmail.com',
            'role' => 'user',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
