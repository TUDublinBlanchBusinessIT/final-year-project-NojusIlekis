<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffClockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'clocked_in_at',
        'clocked_out_at',
        'room_id',
        'notes',
    ];

    protected $casts = [
        'clocked_in_at'  => 'datetime',
        'clocked_out_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function isActive(): bool
    {
        return $this->clocked_out_at === null;
    }

    public function durationMinutes(): int
    {
        $end = $this->clocked_out_at ?? now();
        return (int) $this->clocked_in_at->diffInMinutes($end);
    }

    public function durationLabel(): string
    {
        $mins      = $this->durationMinutes();
        $hours     = intdiv($mins, 60);
        $remaining = $mins % 60;
        if ($hours === 0) {
            return "{$remaining}m";
        }
        return "{$hours}h {$remaining}m";
    }
}
