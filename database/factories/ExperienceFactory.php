<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceFactory extends Factory
{
    protected $model = Experience::class;

    public function definition(): array
    {
        return [
            'role' => fake()->jobTitle(),
            'company' => fake()->company(),
            'location' => fake()->city(),
            'start_date' => (string) fake()->year(),
            'end_date' => fake()->randomElement([(string) fake()->year(), 'Present']),
            'type' => fake()->randomElement(['Full-time', 'Part-time', 'Contract', 'Internship', 'Education']),
            'description' => fake()->paragraph(),
            'tech' => fake()->randomElements(['Laravel', 'Vue.js', 'React', 'PHP', 'MySQL', 'Docker'], fake()->numberBetween(1, 4)),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
