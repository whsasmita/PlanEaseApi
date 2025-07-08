<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification; // Alias untuk menghindari konflik nama
use Kreait\Firebase\Factory;
use App\Models\User;
use App\Models\FcmToken;
use Illuminate\Support\Facades\Log;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $title;
    protected $body;
    protected $data; // Data kustom yang akan disertakan
    protected $user; // Objek User yang menerima notifikasi

    /**
     * Create a new notification instance.
     *
     * @param string $title Judul notifikasi
     * @param string $body Isi notifikasi
     * @param array $data Data kustom yang akan disertakan (opsional)
     * @param App\Models\User $user Objek user yang akan menerima notifikasi
     * @return void
     */
    public function __construct(string $title, string $body, array $data, User $user)
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->user = $user; // Simpan objek user
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['fcm'];
    }

    /**
     * Define how the notification should be sent via FCM.
     *
     * @param  mixed  $notifiable (Instance User yang Notifiable)
     * @return array|null // Mengembalikan array report atau null jika tidak ada token
     */
    public function toFcm($notifiable)
    {
        $fcmTokens = $notifiable->fcmTokens->pluck('fcm_token')->toArray();

        if (empty($fcmTokens)) {
            Log::info("No FCM tokens found for user ID: " . $notifiable->id_user . ". Notification not sent.");
            return null; // Tidak ada token, tidak perlu kirim
        }

        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $messaging = $factory->createMessaging();

        $notification = FirebaseNotification::create($this->title, $this->body);

        try {
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($this->data); // Gunakan data yang di-pass ke konstruktor

            $report = $messaging->sendMulticast($message, $fcmTokens);

            foreach ($report->invalidTokens() as $invalidToken) {
                // Hapus token yang tidak valid
                FcmToken::where('fcm_token', $invalidToken)->delete();
                Log::warning("Invalid FCM Token removed for user ID " . $notifiable->id_user . ": $invalidToken");
            }

            return ['success' => true, 'report' => $report->results()];
        } catch (\Throwable $e) {
            Log::error("Failed to send FCM notification to user ID " . $notifiable->id_user . ": " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}