<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notula extends Model
{
    /** @use HasFactory<\Database\Factories\NotulaFactory> */
    use HasFactory;

    protected $primaryKey = 'id_notula';

    protected $fillable = [
        'title',
        'description',
        'content',
    ];
}
