<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffQualification extends Model
{
    use HasFactory;

    public const TYPES = [
        'education'        => 'Education',
        'garda_vetting'    => 'Garda Vetting',
        'first_aid'        => 'First Aid',
        'child_protection' => 'Child Protection',
        'manual_handling'  => 'Manual Handling',
        'food_safety'      => 'Food Safety',
        'other'            => 'Other',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'issuer',
        'issued_date',
        'expires_at',
        'document_path',
        'notes',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'expires_at'  => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        if (! $this->expires_at) {
            return false;
        }
        return $this->expires_at->isFuture() && $this->expires_at->diffInDays(now()) <= $days;
    }

    public function daysUntilExpiry(): ?int
    {
        if (! $this->expires_at) {
            return null;
        }
        return (int) now()->startOfDay()->diffInDays($this->expires_at->startOfDay(), false);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function statusColour(): string
    {
        if (! $this->expires_at) {
            return 'bg-blue-100 text-blue-800';
        }
        if ($this->isExpired()) {
            return 'bg-red-100 text-red-800';
        }
        if ($this->isExpiringSoon(7)) {
            return 'bg-red-100 text-red-800';
        }
        if ($this->isExpiringSoon(30)) {
            return 'bg-amber-100 text-amber-800';
        }
        return 'bg-green-100 text-green-800';
    }

    public function statusLabel(): string
    {
        if (! $this->expires_at) {
            return __('staff.permanent');
        }
        if ($this->isExpired()) {
            return __('staff.expired');
        }
        $days = $this->daysUntilExpiry();
        if ($days <= 7) {
            return __('staff.expires_in_days', ['days' => $days]);
        }
        if ($days <= 30) {
            return __('staff.expires_in_days', ['days' => $days]);
        }
        return __('staff.valid');
    }
}
