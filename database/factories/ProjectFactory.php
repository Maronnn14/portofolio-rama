<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'short_desc' => fake()->sentence(),
            'full_desc' => fake()->paragraph(),
            'category' => fake()->randomElement(['Frontend', 'Backend', 'Full Stack']),
            'status' => 'published',
            'thumbnail' => fake()->imageUrl(),
            'tech' => fake()->randomElements(['Laravel', 'Vue.js', 'React', 'PHP', 'MySQL', 'Tailwind CSS'], fake()->numberBetween(1, 4)),
            'live_url' => fake()->url(),
            'source_url' => fake()->url(),
            'featured' => fake()->boolean(),
            'gallery' => fake()->randomElements([fake()->imageUrl(), fake()->imageUrl(), fake()->imageUrl()], fake()->numberBetween(1, 3)),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
