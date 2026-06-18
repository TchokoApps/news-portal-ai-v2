<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => $this->faker->randomElement(['super_admin', 'admin', 'editor', 'writer', 'publisher']),
            'remember_token' => null,
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'name' => 'Super Admin',
            'email' => 'superadmin@newsportal.local',
            'role' => 'super_admin',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => 'admin',
        ]);
    }

    public function editor(): static
    {
        return $this->state(fn () => [
            'role' => 'editor',
        ]);
    }

    public function writer(): static
    {
        return $this->state(fn () => [
            'role' => 'writer',
        ]);
    }

    public function publisher(): static
    {
        return $this->state(fn () => [
            'role' => 'publisher',
        ]);
    }
}
