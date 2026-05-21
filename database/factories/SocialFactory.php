<?php

namespace Database\Factories;

use App\Models\Social;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialFactory extends Factory
{
    protected $model = Social::class;

    public function definition(): array
    {
        return [
            'platform' => 'platform_' . fake()->uuid(),
            'url' => fake()->url(),
            'label' => fake()->word(),
            'icon' => strtolower(fake()->word()),
            'visible' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
