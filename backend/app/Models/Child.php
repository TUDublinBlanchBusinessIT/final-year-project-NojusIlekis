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

    public function hasAllergies(): bool
    {
        if (is_array($this->allergies)) {
            return !empty($this->allergies);
        }
        return !empty(trim((string) $this->allergies));
    }

    public function allergyList(): string
    {
        if (is_array($this->allergies)) {
            return implode(', ', $this->allergies);
        }
        return trim((string) $this->allergies) ?: 'None';
    }

    public function allergyArray(): array
    {
        if (is_array($this->allergies)) {
            return $this->allergies;
        }
        if (empty(trim((string) $this->allergies))) {
            return [];
        }
        return array_map('trim', explode(',', $this->allergies));
    }
}