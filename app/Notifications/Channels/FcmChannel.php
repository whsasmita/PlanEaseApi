<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Factory; 
use App\Models\FcmToken;

class FcmChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Pastikan method toFcm ada di notifikasi Anda (misal: NewOrderNotification)
        if (!method_exists($notification, 'toFcm')) {
            throw new \RuntimeException('Notification is missing the toFcm method.');
        }

        // Panggil method toFcm() dari notifikasi untuk mendapatkan CloudMessage
        // Method toFcm() di NewOrderNotification akan mengembalikan array dengan status pengiriman
        $response = $notification->toFcm($notifiable);

        // Anda bisa tambahkan logika logging di sini berdasarkan $response
        if (is_array($response) && isset($response['success']) && !$response['success']) {
            \Log::error("FCM Notification failed to send: " . ($response['error'] ?? 'Unknown error'));
        } else if (is_array($response) && isset($response['success']) && $response['success']) {
            \Log::info("FCM Notification sent successfully.");
            // Jika ingin melihat laporan sukses, Anda bisa cek $response['report']
        }
    }
}