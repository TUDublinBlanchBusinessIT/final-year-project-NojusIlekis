<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    protected $fillable = ['name', 'age_band'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('start_date', 'end_date', 'is_primary')
            ->withTimestamps();
    }

    public function activeCarers(): BelongsToMany
    {
        return $this->users()->wherePivotNull('end_date');
    }

    public function children()
    {
        return $this->hasMany(Child::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function incidentReports()
    {
        return $this->hasMany(\App\Models\IncidentReport::class);
    }
}
