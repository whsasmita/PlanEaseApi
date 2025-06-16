<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollingOption extends Model
{
    /** @use HasFactory<\Database\Factories\PollingOptionFactory> */
    use HasFactory;

    protected $table = 'polling_options';
    protected $primaryKey = 'id_option';

    protected $fillable = [
        'polling_id',
        'option',
    ];

    /**
     * Relasi Many-to-One: Satu PollingOption dimiliki oleh satu Polling.
     */
    public function polling()
    {
        return $this->belongsTo(Polling::class, 'polling_id', 'id_polling');
    }

    /**
     * Relasi One-to-Many: Satu PollingOption memiliki banyak PollingVote.
     */
    public function votes()
    {
        return $this->hasMany(PollingVote::class, 'polling_option_id', 'id_option');
    }
}
