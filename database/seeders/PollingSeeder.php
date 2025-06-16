<?php

namespace Database\Seeders;

use App\Models\Polling;
use App\Models\PollingOption;
use App\Models\PollingVote;
use App\Models\User;
use Illuminate\Database\Seeder;

class PollingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() === 0) {
            User::factory()->count(5)->create();
        }

        Polling::factory()->count(10)->create()->each(function ($polling) {
            $options = PollingOption::factory()->count(rand(3, 5))->create([
                'polling_id' => $polling->id_polling,
            ]);

            $users = User::all();

            $numberOfVotes = rand(0, min(20, $users->count()));

            $voters = $users->random($numberOfVotes);

            foreach ($voters as $user) {
                $randomOption = $options->random();

                PollingVote::factory()->create([
                    'polling_id' => $polling->id_polling,
                    'polling_option_id' => $randomOption->id_option,
                    'user_id' => $user->id_user,
                ]);
            }
        });
    }
}