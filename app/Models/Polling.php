<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Polling extends Model
{
    /** @use HasFactory<\Database\Factories\PollingFactory> */
    use HasFactory;

    protected $primaryKey = 'id_polling';

    protected $fillable = [
        'user_id',
        'title',
        'polling_image',
        'deadline',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function options()
    {
        return $this->hasMany(PollingOption::class, 'polling_id', 'id_polling');
    }
}
