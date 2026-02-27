<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaUpdate extends Model
{
    protected $fillable = [
        'daily_report_id',
        'file_path',
        'type',
        'notes',
        'uploaded_at',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }
}