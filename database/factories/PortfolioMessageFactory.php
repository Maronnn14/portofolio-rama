<?php

namespace Database\Factories;

use App\Models\PortfolioMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class PortfolioMessageFactory extends Factory
{
    protected $model = PortfolioMessage::class;

    public function definition(): array
    {
        return [
            'message_id' => 'msg_' . time() . '_' . substr(md5(uniqid()), 0, 7),
            'name' => fake()->name(),
            'message' => fake()->paragraph(),
            'rating' => fake()->numberBetween(0, 5),
            'posted_at_ms' => (int) (microtime(true) * 1000),
            'session_token' => fake()->uuid(),
            'hidden' => false,
            'flagged' => false,
            'pinned' => false,
        ];
    }
}
