<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduleFactory> */
    use HasFactory;

    protected $primaryKey = 'id_schedule';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // public function notifications()
    // {
    //     return $this->hasMany(Notification::class, 'schedule_id', 'id_schedule');
    // }

    // Scopes untuk query yang lebih mudah
    public function scopeActive($query)
    {
        $today = Carbon::today();
        return $query->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', Carbon::today());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', Carbon::today());
    }

    public function scopeStartingIn($query, int $days)
    {
        $targetDate = Carbon::today()->addDays($days);
        return $query->where('start_date', $targetDate);
    }

    // Check if schedule is active (current date is within the range)
    public function isActive(): bool
    {
        $today = Carbon::today();
        return $today->between($this->start_date, $this->end_date);
    }

    // Check if schedule is upcoming
    public function isUpcoming(): bool
    {
        return Carbon::today()->lt($this->start_date);
    }

    // Check if schedule is past
    public function isPast(): bool
    {
        return Carbon::today()->gt($this->end_date);
    }

    // Get duration in days
    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    // Get formatted date range for display
    public function getFormattedDateRange(): string
    {
        if ($this->start_date->isSameDay($this->end_date)) {
            return $this->start_date->format('d M Y');
        }

        return $this->start_date->format('d M Y') . ' - ' . $this->end_date->format('d M Y');
    }

    // Helper methods untuk notification system
    public function getDaysUntilStart(): int
    {
        return Carbon::today()->diffInDays($this->start_date, false);
    }

    public function getDaysUntilEnd(): int
    {
        return Carbon::today()->diffInDays($this->end_date, false);
    }

    // Cek apakah perlu notification di hari tertentu
    public function shouldNotifyIn(int $days): bool
    {
        return $this->start_date->equalTo(Carbon::today()->addDays($days));
    }
}