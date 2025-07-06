<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class FcmTokenController extends Controller
{
    /**
     * Store or update the FCM token for the authenticated user.
     * Menyimpan atau memperbarui token FCM untuk pengguna yang terautentikasi.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        // Cari token yang sudah ada untuk pengguna ini dan token spesifik ini
        // Jika token sudah ada, perbarui timestamps. Jika tidak, buat yang baru.
        $fcmToken = FcmToken::updateOrCreate(
            [
                'user_id' => $user->id_user,
                'fcm_token' => $request->fcm_token,
            ],
            [
                // Tidak ada yang perlu diupdate selain timestamps jika sudah ada
            ]
        );

        return response()->json([
            'message' => 'FCM token stored/updated successfully.',
            'fcm_token_id' => $fcmToken->id,
        ], Response::HTTP_OK);
    }

    /**
     * Delete the FCM token for the authenticated user.
     * Menghapus token FCM untuk pengguna yang terautentikasi.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        $deleted = FcmToken::where('user_id', $user->id_user)
                           ->where('fcm_token', $request->fcm_token)
                           ->delete();

        if ($deleted) {
            return response()->json([
                'message' => 'FCM token deleted successfully.'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => 'FCM token not found or already deleted.'
        ], Response::HTTP_NOT_FOUND);
    }
}
