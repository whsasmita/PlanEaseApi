<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\PngEncoder;

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

        $img->text($initial, 100, 100, function ($font) {
            $font->file(public_path('fonts/Roboto_Condensed-Bold.ttf'));
            $font->size(100);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });

        $encodedImage = $img->encode(new PngEncoder());

        $filename = 'profile_photos/' . md5($user->full_name . time()) . '.png';
        Storage::disk('public')->put($filename, $encodedImage);

        $user->profile()->create([
            'photo_profile' => $filename,
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
