<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = [
            ['name' => 'Anatomy', 'code' => 'ANAT', 'description' => 'Human anatomy and structure'],
            ['name' => 'Physiology', 'code' => 'PHYS', 'description' => 'Human physiology and functions'],
            ['name' => 'Biochemistry', 'code' => 'BIOC', 'description' => 'Medical biochemistry'],
            ['name' => 'Pathology', 'code' => 'PATH', 'description' => 'Disease mechanisms'],
            ['name' => 'Pharmacology', 'code' => 'PHAR', 'description' => 'Drug mechanisms'],
            ['name' => 'Internal Medicine', 'code' => 'IMED', 'description' => 'Clinical medicine'],
            ['name' => 'Surgery', 'code' => 'SURG', 'description' => 'Surgical procedures'],
        ];

        $dept = fake()->randomElement($departments);

        return [
            'name' => $dept['name'],
            'code' => $dept['code'],
            'description' => $dept['description'],
        ];
    }
}