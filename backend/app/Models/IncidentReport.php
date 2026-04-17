<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Acknowledgement;

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

    public function acknowledgement()
    {
        return $this->hasOne(Acknowledgement::class, 'record_id')
                    ->where('record_type', 'incident_report');
    }

    /**
     * Check if this incident is overdue (open for 10+ days).
     */
    public function isOverdue(): bool
    {
        if ($this->status === 'closed') return false;
        return $this->incident_date && \Carbon\Carbon::parse($this->incident_date)->diffInDays(now()) >= 10;
    }

    /**
     * Get how many days this incident has been open.
     */
    public function daysOpen(): int
    {
        return $this->incident_date ? (int) \Carbon\Carbon::parse($this->incident_date)->diffInDays(now()) : 0;
    }

    /**
     * Scope: overdue incidents (open/reviewed for 10+ days).
     */
    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['open', 'reviewed'])
                     ->where('incident_date', '<=', now()->subDays(10));
    }
}