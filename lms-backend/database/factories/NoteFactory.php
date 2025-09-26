<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'title' => fake()->sentence(4),
            'file_url' => 'https://s3.amazonaws.com/lms-notes/' . fake()->uuid() . '.pdf',
            'uploaded_by' => User::factory(),
            'uploaded_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'version' => fake()->numberBetween(1, 3),
        ];
    }
}