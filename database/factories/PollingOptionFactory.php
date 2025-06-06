<?php

namespace Database\Factories;

use App\Models\Polling;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PollingOption>
 */
class PollingOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'polling_id' => Polling::factory(),
            'option' => fake()->sentence(3),
        ];
    }
}
