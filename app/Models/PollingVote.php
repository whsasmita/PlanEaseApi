<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollingVote extends Model
{
    use HasFactory;

    protected $table = 'polling_votes';
    protected $primaryKey = 'id_vote';

    protected $fillable = [
        'polling_id',
        'polling_option_id',
        'user_id',
    ];

    /**
     * Relasi Many-to-One: Satu PollingVote dimiliki oleh satu Polling.
     */
    public function polling()
    {
        return $this->belongsTo(Polling::class, 'polling_id', 'id_polling');
    }

    /**
     * Relasi Many-to-One: Satu PollingVote dimiliki oleh satu PollingOption.
     */
    public function option()
    {
        return $this->belongsTo(PollingOption::class, 'polling_option_id', 'id_option');
    }

    /**
     * Relasi Many-to-One: Satu PollingVote dimiliki oleh satu User (jika user_id tidak null).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}