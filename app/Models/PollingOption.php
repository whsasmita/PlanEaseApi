<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollingOption extends Model
{
    /** @use HasFactory<\Database\Factories\PollingOptionFactory> */
    use HasFactory;

    protected $primaryKey = 'id_option';

    protected $fillable = [
        'polling_id',
        'option',
    ];

    public function polling()
    {
        return $this->belongsTo(Polling::class, 'polling_id', 'id_polling');
    }
}
