<?php

namespace Database\Factories;

use App\Models\Polling;
use App\Models\PollingOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PollingOption>
 */
class PollingOptionFactory extends Factory
{
    protected $model = PollingOption::class;

    public function definition(): array
    {
        return [
            // 'polling_id' akan diisi saat dipanggil dari seeder
            'option' => $this->faker->word() . ' ' . $this->faker->word(),
        ];
    }
}
