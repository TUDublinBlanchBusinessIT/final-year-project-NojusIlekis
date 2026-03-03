<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyUpdate extends Model
{
    protected $fillable = [
        'child_id',
        'date',
        'meals',
        'sleep',
        'notes',
        'created_by',
    ];
}
