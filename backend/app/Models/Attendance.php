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
    ];
}
