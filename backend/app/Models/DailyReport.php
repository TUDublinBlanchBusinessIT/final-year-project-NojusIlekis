<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    protected $fillable = [
        'child_id',
        'carer_id',
        'date',
        'daily_report',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function carer()
    {
        return $this->belongsTo(User::class, 'carer_id');
    }

    public function mediaUpdates()
    {
        return $this->hasMany(MediaUpdate::class);
    }
}