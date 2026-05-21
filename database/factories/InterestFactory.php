<?php

namespace Database\Factories;

use App\Models\Interest;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterestFactory extends Factory
{
    protected $model = Interest::class;

    public function definition(): array
    {
        return [
            'name' => 'interest_' . fake()->uuid(),
            'icon' => fake()->randomElement(['🎮', '📚', '🎵', '🏃', '🎨', '🌐']),
            'description' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
