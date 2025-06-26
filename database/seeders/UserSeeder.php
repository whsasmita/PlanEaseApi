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
            'full_name' => 'Admin',
            'email' => 'admin@undiksha.ac.id',
            'password' => bcrypt('admin123'),
            'role' => 'ADMIN',
            'phone' => '081234567890',
        ]);
        User::factory()->create([
            'full_name' => 'Member',
            'email' => 'member@undiksha.ac.id',
            'password' => bcrypt('member123'),
            'role' => 'MEMBER',
            'phone' => '081234567890',
        ]);
    }
}
