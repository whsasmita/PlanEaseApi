<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $notificationData = $this->data;

        return [
            'id_notification' => $this->id, 
            'type' => $this->type, 
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Ambil detail notifikasi dari kolom 'data'
            'schedule_id' => $notificationData['schedule_id'] ?? null,
            'title' => $notificationData['title'] ?? null,
            'content' => $notificationData['content'] ?? null,

            // Data jadwal yang tersimpan di dalam data notifikasi
            'schedule' => [
                'id' => $notificationData['schedule']['id'] ?? null,
                'title' => $notificationData['schedule']['title'] ?? null,
                'start_date' => $notificationData['schedule']['start_date'] ?? null,
                'end_date' => $notificationData['schedule']['end_date'] ?? null,
            ],
            // Anda bisa menambahkan relasi user yang notifikasi ini miliki
            // 'user' => new UserResource($this->whenLoaded('notifiable')) // Jika notifiable adalah user
        ];
    }
}