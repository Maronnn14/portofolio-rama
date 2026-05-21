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
            'icon' => fake()->randomElement([
                'https://cdn-icons-png.flaticon.com/128/5968/5968891.png',
                'https://cdn-icons-png.flaticon.com/128/1042/1042344.png',
                'https://cdn-icons-png.flaticon.com/128/686/686589.png',
                'https://cdn-icons-png.flaticon.com/128/2995/2995085.png',
                'https://cdn-icons-png.flaticon.com/128/2232/2232688.png',
                'https://cdn-icons-png.flaticon.com/128/2920/2920061.png',
            ]),
            'description' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
