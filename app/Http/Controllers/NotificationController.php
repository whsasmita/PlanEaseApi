<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseNotificationService;
use App\Models\User; // Impor model User
use App\Notifications\NewOrderNotification; // Impor notifikasi yang sudah Anda buat
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $firebaseNotificationService;

    public function __construct(FirebaseNotificationService $firebaseNotificationService)
    {
        $this->firebaseNotificationService = $firebaseNotificationService;
    }

    /**
     * Mengirim notifikasi tes ke user berdasarkan user_id.
     * Menggunakan sistem notifikasi Laravel.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendTestNotificationToUser(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id_user', // Validasi user_id (sesuaikan `id_user` dengan primary key di tabel `users` Anda)
            'title' => 'required|string|max:255', // Judul notifikasi
            'body' => 'required|string',         // Isi notifikasi
            'data' => 'sometimes|array',         // Data kustom (opsional)
        ]);

        $userId = $request->input('user_id');
        $title = $request->input('title');
        $body = $request->input('body');
        $data = $request->input('data', []); // Default array kosong jika tidak ada data

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Pastikan user memiliki FCM token
        if ($user->fcmTokens->isEmpty()) {
            return response()->json(['message' => 'No FCM tokens found for this user.'], 400);
        }

        try {
            // Menggunakan sistem notifikasi Laravel
            // Pass judul, isi, dan data langsung ke konstruktor NewOrderNotification
            $user->notify(new NewOrderNotification($title, $body, $data, $user));

            return response()->json([
                'message' => 'Notification successfully queued/sent via Laravel Notifications.',
                'user_id' => $userId,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error sending notification via Laravel Notifications: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send notification via Laravel Notifications.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengirim notifikasi ke topik tertentu (menggunakan FirebaseNotificationService secara langsung).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendTopicNotification(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
            'data' => 'sometimes|array',
        ]);

        $topic = $request->input('topic');
        $title = $request->input('title');
        $body = $request->input('body');
        $data = $request->input('data', []);

        $result = $this->firebaseNotificationService->sendToTopic($topic, $title, $body, $data);

        if ($result['success']) {
            return response()->json(['message' => 'Topic notification sent successfully!', 'report' => $result['report']]);
        } else {
            return response()->json(['message' => 'Failed to send topic notification', 'error' => $result['error']], 500);
        }
    }
}