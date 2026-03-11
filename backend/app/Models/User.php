<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function dashboardRouteName(): string
    {
        return match ($this->role) {
            'manager' => 'manager.dashboard',
            'carer'   => 'carer.dashboard',
            default   => 'parent.dashboard',
        };
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class)
            ->withPivot('start_date', 'end_date', 'is_primary')
            ->withTimestamps();
    }

    public function activeRooms(): BelongsToMany
    {
        return $this->rooms()->wherePivotNull('end_date');
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Child::class, 'child_parent', 'parent_id', 'child_id')
            ->withPivot('relationship_type', 'legal_guardian')
            ->withTimestamps();
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class, 'carer_id');
    }

    public function attendancesRecorded()
    {
        return $this->hasMany(Attendance::class, 'recorded_by');
    }

    public function dailyUpdates()
    {
        return $this->hasMany(DailyUpdate::class, 'created_by');
    }

    public function medicationLogs()
    {
        return $this->hasMany(MedicationLog::class, 'carer_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'parent_id');
    }

    public function incidentReports()
    {
        return $this->hasMany(\App\Models\IncidentReport::class, 'carer_id');
    }
}