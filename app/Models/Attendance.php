<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'user_id',
        'status',
        'joined_at',
        'left_at',
        'duration_minutes',
        'notes',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markPresent(): void
    {
        $this->update([
            'status' => 'present',
            'joined_at' => now(),
        ]);
    }

    public function markAbsent(): void
    {
        $this->update([
            'status' => 'absent',
        ]);
    }

    public function markLate(): void
    {
        $this->update([
            'status' => 'late',
            'joined_at' => now(),
        ]);
    }

    public function leave(): void
    {
        $this->update([
            'left_at' => now(),
            'duration_minutes' => $this->joined_at ? $this->joined_at->diffInMinutes(now()) : 0,
        ]);
    }
}
