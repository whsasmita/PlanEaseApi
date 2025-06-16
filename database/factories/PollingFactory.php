<?php

namespace Database\Factories;

use App\Models\Polling;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Polling>
 */
class PollingFactory extends Factory
{
    protected $model = Polling::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id_user ?? User::factory()->create()->id_user,
            'title' => $this->faker->sentence(rand(3, 7)),
            'description' => $this->faker->paragraph(rand(3, 7)),
            'polling_image' => $this->faker->boolean(50) ? $this->faker->imageUrl(640, 480, 'poll') : null,
            'deadline' => $this->faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d'),
        ];
    }
}
