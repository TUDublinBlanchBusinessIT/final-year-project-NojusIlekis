<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildMilestone extends Model
{
    protected $fillable = [
        'child_id', 'milestone_id', 'observed_by', 'observed_at', 'notes',
    ];

    protected $casts = [
        'observed_at' => 'date',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function observer()
    {
        return $this->belongsTo(User::class, 'observed_by');
    }
}
