<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role_id' => Role::factory(),
            'department_id' => Department::factory(),
            'current_year' => null,
            'enrollment_number' => null,
            'photo_url' => null,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
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

    /**
     * Create a student user.
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::where('name', 'student')->first()?->id ?? Role::factory()->create(['name' => 'student'])->id,
            'current_year' => fake()->numberBetween(1, 4),
            'enrollment_number' => 'MED' . fake()->year() . fake()->unique()->numberBetween(1000, 9999),
        ]);
    }

    /**
     * Create a faculty user.
     */
    public function faculty(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::where('name', 'faculty')->first()?->id ?? Role::factory()->create(['name' => 'faculty'])->id,
            'current_year' => null,
            'enrollment_number' => null,
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::where('name', 'admin')->first()?->id ?? Role::factory()->create(['name' => 'admin'])->id,
            'current_year' => null,
            'enrollment_number' => null,
        ]);
    }
}
