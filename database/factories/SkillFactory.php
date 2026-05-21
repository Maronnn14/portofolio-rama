<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        return [
            'name' => 'Skill_' . fake()->bothify('?????-#####'),
            'category' => fake()->randomElement(['Frontend', 'Backend', 'Tools', 'Design']),
            'icon' => fake()->word(),
            'teaser' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'proficiency' => fake()->numberBetween(0, 100),
            'level' => fake()->randomElement(['Beginner', 'Intermediate', 'Advanced', 'Expert']),
            'related_projects' => fake()->randomElements([fake()->word(), fake()->word()], fake()->numberBetween(0, 3)),
            'gallery' => fake()->randomElements([fake()->imageUrl(), fake()->imageUrl()], fake()->numberBetween(0, 2)),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
