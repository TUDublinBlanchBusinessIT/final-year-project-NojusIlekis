<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Child extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'dob',
        'allergies',
        'medical_notes',
        'room_id',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function parents()
    {
        return $this->belongsToMany(User::class, 'child_parent', 'child_id', 'parent_id')
            ->withPivot('relationship_type', 'legal_guardian')
            ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function dailyUpdates()
    {
        return $this->hasMany(DailyUpdate::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }

    public function medicationLogs()
    {
        return $this->hasMany(MedicationLog::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function incidentReports()
    {
        return $this->hasMany(\App\Models\IncidentReport::class);
    }
}