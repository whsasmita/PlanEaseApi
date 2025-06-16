<?php

namespace Database\Factories;

use App\Models\PollingVote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollingVoteFactory extends Factory
{
    protected $model = PollingVote::class;

    public function definition(): array
    {
        // 'polling_id' dan 'polling_option_id' akan diisi saat dipanggil dari seeder
        return [
            // Jika Anda ingin setiap vote selalu memiliki user_id:
            // 'user_id' => User::inRandomOrder()->first()->id_user ?? User::factory()->create()->id_user,

            // Jika user_id bisa null (opsional seperti di migrasi):
            'user_id' => $this->faker->boolean(80) ? (User::inRandomOrder()->first()->id_user ?? User::factory()->create()->id_user) : null,
        ];
    }
}