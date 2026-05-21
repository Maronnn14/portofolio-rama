<?php

namespace Database\Factories;

use App\Models\GalleryImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryImageFactory extends Factory
{
    protected $model = GalleryImage::class;

    public function definition(): array
    {
        return [
            'url' => fake()->imageUrl(),
            'alt' => fake()->sentence(),
            'category' => fake()->randomElement(['Nature', 'Tech', 'Urban', 'People']),
            'visible' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
