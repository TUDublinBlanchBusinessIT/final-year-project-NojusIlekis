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

    public function milestones()
    {
        return $this->belongsToMany(Milestone::class, 'child_milestones')
                     ->withPivot(['observed_by', 'observed_at', 'notes'])
                     ->withTimestamps();
    }

    public function getAgeInMonthsAttribute(): int
    {
        return $this->dob ? (int) $this->dob->diffInMonths(now()) : 0;
    }

    public function getAgeRangeAttribute(): string
    {
        $months = $this->age_in_months;
        if ($months < 12) return '0-12';
        if ($months < 24) return '12-24';
        if ($months < 36) return '24-36';
        if ($months < 48) return '36-48';
        return '48-60';
    }

    public function milestoneProgress(?string $category = null): array
    {
        $ageRange = $this->age_range;

        $query = Milestone::where('age_range_months', $ageRange);
        if ($category) {
            $query->where('category', $category);
        }
        $total = $query->count();

        $achievedQuery = $this->milestones()->where('age_range_months', $ageRange);
        if ($category) {
            $achievedQuery->where('category', $category);
        }
        $achieved = $achievedQuery->count();

        return [
            'total'      => $total,
            'achieved'   => $achieved,
            'percentage' => $total > 0 ? round(($achieved / $total) * 100) : 0,
        ];
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