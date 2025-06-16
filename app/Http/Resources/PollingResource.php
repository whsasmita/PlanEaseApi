<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PollingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_polling,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->polling_image ? asset('storage/' . $this->polling_image) : null, 
            'deadline' => $this->deadline->format('Y-m-d H:i:s'), 
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            'author' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id_user,
                    'full_name' => $this->user->full_name,
                    'email' => $this->user->email,
                ];
            }),

            'options' => $this->whenLoaded('options', function () {
                return $this->options->map(function ($option) {
                    return [
                        'id' => $option->id_option,
                        'option_text' => $option->option,
                        'vote_count' => $option->votes->count(), 
                    ];
                });
            }),
            
            'is_open' => $this->deadline->isFuture(),
            'time_remaining' => $this->deadline->diffForHumans(),
        ];
    }
}