<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'ADMIN')->first();

    if ($admin) {
        // Buat beberapa schedule dengan variasi
        Schedule::factory()->count(3)->create(); // Random dates
        Schedule::factory()->startingIn(7)->count(2)->create(); // H-7 untuk testing notification
        Schedule::factory()->startingIn(3)->count(1)->create(); // H-3
        Schedule::factory()->startingIn(1)->count(1)->create(); // H-1
        Schedule::factory()->startingToday()->count(1)->create(); // Hari ini
        Schedule::factory()->singleDay()->count(1)->create(); // Single day event
    }
    }
}
