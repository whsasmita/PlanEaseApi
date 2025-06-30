<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileController extends Controller
{
    // This controller is used to manage user profiles.
    // It requires authentication to access its methods.
    public function __construct()
    {
        $this->middleware('auth:api')->except(['getPhoto']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::user()->hasRole('ADMIN')) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk melihat semua profil.'], 403);
        }

        $profiles = Profile::all();

        if ($profiles->isEmpty()) {
            return response()->json(['message' => 'Tidak ada profil yang ditemukan.'], 404);
        }

        return response()->json([
            'message' => 'Semua profil berhasil diambil.',
            'profiles' => ProfileResource::collection($profiles)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Profile $profile)
    {
        Log::info('User class: ' . get_class(Auth::user()));

        if ($profile->user_id !== Auth::id() && !Auth::user()->hasRole('ADMIN')) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk melihat profil ini.'], 403);
        }

        return response()->json([
            'message' => 'Profil berhasil diambil.',
            'profile' => new ProfileResource($profile)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileUpdateRequest $request, Profile $profile)
    {
        $user = Auth::user();

        if ($profile->user_id !== $user->id_user) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk memperbarui profil ini.'], 403);
        }

        $validatedData = $request->validated();

        if ($request->hasFile('photo_profile')) {
            if ($profile->photo_profile && Storage::disk('public')->exists($profile->photo_profile)) {
                Storage::disk('public')->delete($profile->photo_profile);
            }

            $path = $request->file('photo_profile')->store('profile_photos', 'public');
            $profile->photo_profile = $path;
        }

        $profile->fill([
            'division' => $validatedData['division'],
            'position' => $validatedData['position'],
        ]);

        $profile->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'profile' => new ProfileResource($profile)
        ]);
    }

    /**
     * Mengembalikan foto profil.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function getPhoto(Profile $profile): BinaryFileResponse|\Illuminate\Http\JsonResponse
    {
        if ($profile->user_id !== Auth::id() && !Auth::user()->hasRole('ADMIN')) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk melihat foto ini.'], 403);
        }

        if (!$profile->photo_profile || !Storage::disk('public')->exists($profile->photo_profile)) {
            $defaultImagePath = public_path('images/default_profile.png');
            if (file_exists($defaultImagePath)) {
                return response()->file($defaultImagePath);
            }
            return response()->json(['message' => 'Foto profil tidak ditemukan.'], 404);
        }

        $path = Storage::disk('public')->path($profile->photo_profile);
        return response()->file($path);
    }
}
