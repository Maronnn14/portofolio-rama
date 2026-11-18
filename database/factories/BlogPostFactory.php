<?php

namespace Database\Factories;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = rtrim(fake()->sentence(4), '.');

        return [
            'title' => $title,
            'slug' => null,
            'category' => fake()->randomElement(['Daily Note', 'Devlog', 'Tutorial', 'Reflection']),
            'excerpt' => fake()->paragraph(),
            'body' => "## ".fake()->sentence(4)."\n\n".fake()->paragraph(4)."\n\n- ".fake()->sentence(3)."\n- ".fake()->sentence(3),
            'cover_image' => null,
            'tags' => fake()->randomElements(['Laravel', 'Vue', 'CSS', 'Karier', 'Tips'], fake()->numberBetween(0, 3)),
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-2 months', 'now'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }
}
