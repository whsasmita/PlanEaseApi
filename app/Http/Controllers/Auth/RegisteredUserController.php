<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\PngEncoder;
use Illuminate\Support\Str;

class RegisteredUserController
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'MEMBER'
        ]);

        $defaultDivision = 'Tidak ada keterangan';
        $defaultPosition = 'Tidak ada keterangan';

        $initial = strtoupper(substr($user->full_name, 0, 1));
        $randomColor = sprintf('#%06X', mt_rand(0, 0xFFFFFF));

        $img = Image::create(200, 200)->fill($randomColor);

        $fontPath = public_path('fonts/Roboto_Condensed-Bold.ttf');
        if (!file_exists($fontPath)) {
            Log::error("Font file not found at: " . $fontPath);
            $fontPath = null;
        }

        $img->text($initial, 100, 100, function ($font) use ($fontPath) {
            if ($fontPath) {
                $font->file($fontPath);
            }
            $font->size(100);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });

        $encodedImage = $img->encode(new PngEncoder());

        $destinationDir = public_path('profile_photos');

        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }

        $imageName = md5($user->full_name . time() . Str::random(5)) . '.png';
        $filePath = $destinationDir . '/' . $imageName;

        file_put_contents($filePath, $encodedImage);

        $photoProfilePath = 'profile_photos/' . $imageName;

        $user->profile()->create([
            'photo_profile' => $photoProfilePath,
            'division' => $defaultDivision,
            'position' => $defaultPosition,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Registration successful.',
            'user' => Arr::only($user->toArray(), ['id_user', 'full_name', 'email', 'phone', 'role']),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60
        ], 201);
    }
}
