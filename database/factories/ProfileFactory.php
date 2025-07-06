<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sourceImages = [
            public_path('seeder_photos/dummy_photos1.png'),
            public_path('seeder_photos/dummy_photos2.png'),
        ];

        $randomImagePath = fake()->randomElement($sourceImages);

        $destinationDir = public_path('profile_photos');

        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }

        $imageName = Str::random(40) . '.png';
        $filePath = $destinationDir . '/' . $imageName;

        file_put_contents($filePath, file_get_contents($randomImagePath));

        $databasePath = 'profile_photos/' . $imageName;

        return [
            'user_id' => User::factory(),
            'photo_profile' => $databasePath,
            'position' => fake()->jobTitle(),
            'division' => fake()->company(),
        ];
    }
}
