<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Arr;

class AuthenticatedSessionController
{
    /**
     * Handle an incoming authentication request.
     * Mengembalikan token JWT setelah login.
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi input
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid credentials.',
                ], 401); 
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Could not create token.',
                'error' => $e->getMessage()
            ], 500);
        }

        $user = Auth::user();

        if (is_null($user)) {
            return response()->json([
                'message' => 'Login successful, but user data could not be retrieved from Auth facade. Check JWT config.',
            ], 500);
        }
        return response()->json([
            'message' => 'Login successful.',
            'user' => Arr::only($user->toArray(), ['id', 'full_name', 'email', 'phone', 'role']),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60
        ], 200);
    }

    /**
     * Destroy an authenticated session (for JWT token).
     * Membatalkan token JWT (menambahkannya ke daftar hitam).
     */
    public function destroy(): JsonResponse
    {
        Auth::logout();

        return response()->json([
            'message' => 'Logout successful.',
        ], 200);
    }


    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        return response()->json([
            'access_token' => JWTAuth::refresh(),
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }
}
