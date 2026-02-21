<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaUpdate extends Model
{
    public function dailyReport()
{
    return $this->belongsTo(DailyReport::class);
}
}
