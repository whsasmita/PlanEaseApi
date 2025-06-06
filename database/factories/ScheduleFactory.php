<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, $startDate->format('Y-m-d') . ' +7 days');

        return [
            'title' => fake()->sentence(4, false),
            'description' => fake()->text(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];
    }

    /**
     * Create a schedule starting today
     */
    public function startingToday(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = Carbon::today();
            $endDate = fake()->dateTimeBetween($startDate, '+7 days');
            
            return [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ];
        });
    }

    /**
     * Create a schedule starting in X days (useful for testing notifications)
     */
    public function startingIn(int $days): static
    {
        return $this->state(function (array $attributes) use ($days) {
            $startDate = Carbon::today()->addDays($days);
            $endDate = fake()->dateTimeBetween($startDate, $startDate->copy()->addDays(7));
            
            return [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ];
        });
    }

    /**
     * Create a single day event
     */
    public function singleDay(): static
    {
        return $this->state(function (array $attributes) {
            $date = fake()->dateTimeBetween('now', '+1 month');
            
            return [
                'start_date' => $date->format('Y-m-d'),
                'end_date' => $date->format('Y-m-d'),
            ];
        });
    }
}