<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = [
            ['name' => 'student', 'display_name' => 'Student', 'description' => 'Medical student'],
            ['name' => 'faculty', 'display_name' => 'Faculty', 'description' => 'Teaching faculty'],
            ['name' => 'hod', 'display_name' => 'Head of Department', 'description' => 'Department head'],
            ['name' => 'principal', 'display_name' => 'Principal', 'description' => 'College principal'],
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'System administrator'],
        ];

        $role = fake()->randomElement($roles);

        return [
            'name' => $role['name'],
            'display_name' => $role['display_name'],
            'description' => $role['description'],
        ];
    }
}