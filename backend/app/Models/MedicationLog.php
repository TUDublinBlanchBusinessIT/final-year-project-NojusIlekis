<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicationLog extends Model
{
    protected $fillable = [
        'child_id',
        'carer_id',
        'medication_name',
        'dosage',
        'date',
        'time_given',
        'notes',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function carer()
    {
        return $this->belongsTo(User::class, 'carer_id');
    }
}