<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        unset($data['photo_profile']);

        if ($this->photo_profile && file_exists(public_path($this->photo_profile))) {
            $data['photo_profile_url'] = route('profile.photo', ['profile' => $this->id_profile]);
        } else {
            $data['photo_profile_url'] = asset('images/default_profile.png');
        }

        if ($this->whenLoaded('user')) {
            $data['user'] = [
                'id_user' => $this->user->id_user,
                'full_name' => $this->user->full_name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'role' => $this->user->role,
            ];
        }

        $data['created_at'] = $this->created_at->format('Y-m-d');
        $data['updated_at'] = $this->updated_at->format('Y-m-d');

        return $data;
    }
}
