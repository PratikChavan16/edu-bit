<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['mcq', 'saq', 'laq'];
        $competencies = [
            'Medical Knowledge',
            'Clinical Skills', 
            'Communication Skills',
            'Professionalism',
            'Systems-based Practice'
        ];

        return [
            'subject_id' => Subject::factory(),
            'title' => fake()->sentence(3) . ' Assessment',
            'type' => fake()->randomElement($types),
            'competency_tags' => fake()->randomElements($competencies, fake()->numberBetween(1, 3)),
            'author_id' => User::factory(),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90, 120]),
            'start_at' => fake()->dateTimeBetween('now', '+1 month'),
            'end_at' => fake()->dateTimeBetween('+1 month', '+2 months'),
        ];
    }
}