<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'full_name' => 'Admin BEM FTK Undiksha',
            'email' => 'ftk@undiksha.ac.id',
            'role' => 'ADMIN',
        ]);
    }
}
