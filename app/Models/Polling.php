<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Polling extends Model
{
    use HasFactory;

    // Menentukan nama tabel jika tidak sesuai dengan konvensi Laravel (plural dari nama model)
    protected $table = 'pollings';

    // Menentukan primary key jika bukan 'id'
    protected $primaryKey = 'id_polling';

    // Menentukan kolom yang boleh diisi secara massal (mass assignable)
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'polling_image',
        'deadline',
    ];

    // Kolom tanggal yang harus diubah menjadi instance Carbon
    protected $dates = [
        'deadline',
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
