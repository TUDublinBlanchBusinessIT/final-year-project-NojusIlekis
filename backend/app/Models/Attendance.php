<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'child_id',
        'date',
        'status',
        'check_in_at',
        'check_out_at',
        'recorded_by',
        'room_id',
    ];

    protected $casts = [
        'date'         => 'date',
        'check_in_at'  => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
