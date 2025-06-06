<?php

namespace Database\Seeders;

use App\Models\Polling;
use App\Models\PollingOption;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PollingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            $pollings = Polling::factory(rand(1, 2))->create([
                'user_id' => $user->id_user,
            ]);
            
            foreach ($pollings as $polling) {
                PollingOption::factory(rand(2, 5))->create([
                    'polling_id' => $polling->id_polling,
                ]);
            }
        }
    }
}
