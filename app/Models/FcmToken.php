<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fcm_token',
    ];

    /**
     * Relasi Many-to-One: Satu FcmToken dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
