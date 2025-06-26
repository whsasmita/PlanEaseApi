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
            storage_path('app/profile_photos/dummy_photos1.png'),
            storage_path('app/profile_photos/dummy_photos2.png'),
        ];

        $randomImagePath = fake()->randomElement($sourceImages);

        $destinationFilename = 'profile_photos/' . Str::random(40) . '.png';

        Storage::disk('public')->put(
            $destinationFilename,
            file_get_contents($randomImagePath)
        );

        return [
            'user_id' => User::factory(),
            'photo_profile' => $destinationFilename,
            'position' => fake()->jobTitle(),
            'division' => fake()->company(),
        ];
    }
}
