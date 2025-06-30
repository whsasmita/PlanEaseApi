<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Polling extends Model
{
    use HasFactory;

    protected $table = 'pollings';

    protected $primaryKey = 'id_polling';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'polling_image',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    /**
     * Relasi One-to-Many: Satu Polling dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Relasi One-to-Many: Satu Polling memiliki banyak PollingOption.
     */
    public function options()
    {
        return $this->hasMany(PollingOption::class, 'polling_id', 'id_polling');
    }

    /**
     * Relasi One-to-Many: Satu Polling memiliki banyak PollingVote.
     */
    public function votes()
    {
        return $this->hasMany(PollingVote::class, 'polling_id', 'id_polling');
    }
}
