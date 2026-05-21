<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'age_band', 'capacity', 'description', 'max_children_per_staff'];

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

    public function presentChildrenToday(): int
    {
        return Attendance::where('room_id', $this->id)
            ->where('date', today())
            ->where('status', 'present')
            ->count();
    }

    public function currentlyClockedInStaff()
    {
        $assignedCarerIds = $this->users()
            ->wherePivotNull('end_date')
            ->where('users.role', 'carer')
            ->pluck('users.id');

        return User::whereIn('id', $assignedCarerIds)
            ->whereHas('clockIns', fn ($q) => $q->whereNull('clocked_out_at'))
            ->get();
    }

    public function currentRatio(): array
    {
        $children    = $this->presentChildrenToday();
        $staff       = $this->currentlyClockedInStaff()->count();
        $max         = $this->max_children_per_staff ?: 6;
        $required    = $children > 0 ? max(1, (int) ceil($children / $max)) : 0;
        $isCompliant = $staff >= $required;

        return [
            'children'      => $children,
            'staff'         => $staff,
            'required'      => $required,
            'max_per_staff' => $max,
            'compliant'     => $isCompliant,
            'shortfall'     => max(0, $required - $staff),
        ];
    }
}
