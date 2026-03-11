<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    protected $fillable = [
        'child_id',
        'carer_id',
        'room_id',
        'incident_date',
        'incident_time',
        'title',
        'description',
        'action_taken',
        'severity',
        'parent_contact_required',
        'status',
    ];

    protected $casts = [
        'parent_contact_required' => 'boolean',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function carer()
    {
        return $this->belongsTo(User::class, 'carer_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}