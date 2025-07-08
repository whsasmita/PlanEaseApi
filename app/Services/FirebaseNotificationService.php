<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $credentialsPath = config('firebase.credentials');
        Log::info("Attempting to load Firebase credentials from: " . $credentialsPath);

        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Mengirim push notification ke perangkat tertentu (menggunakan FCM Token).
     *
     * @param string $deviceToken FCM Token dari perangkat target
     * @param string $title Judul notifikasi
     * @param string $body Isi notifikasi
     * @param array $data Data kustom yang akan disertakan (opsional)
     * @return array Respon dari Firebase
     */
    public function sendToDevice(string $deviceToken, string $title, string $body, array $data = []): array
    {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        try {
            $sendReport = $this->messaging->send($message);
            // Anda bisa log atau memproses sendReport sesuai kebutuhan
            return ['success' => true, 'report' => $sendReport->results()];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Mengirim push notification ke topik tertentu.
     *
     * @param string $topic Nama topik
     * @param string $title Judul notifikasi
     * @param string $body Isi notifikasi
     * @param array $data Data kustom yang akan disertakan (opsional)
     * @return array Respon dari Firebase
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): array
    {
        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        try {
            $sendReport = $this->messaging->send($message);
            return ['success' => true, 'report' => $sendReport->results()];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Mengirim pesan data saja (tanpa notifikasi UI yang langsung terlihat).
     *
     * @param string $deviceToken FCM Token dari perangkat target
     * @param array $data Data kustom yang akan disertakan
     * @return array Respon dari Firebase
     */
    public function sendDataToDevice(string $deviceToken, array $data): array
    {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withData($data);

        try {
            $sendReport = $this->messaging->send($message);
            return ['success' => true, 'report' => $sendReport->results()];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
