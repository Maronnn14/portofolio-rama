<?php

namespace Database\Factories;

use App\Models\PersonalInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalInfoFactory extends Factory
{
    protected $model = PersonalInfo::class;

    public function definition(): array
    {
        return [
            'key' => 'key_' . fake()->uuid(),
            'value' => fake()->sentence(),
        ];
    }
}
