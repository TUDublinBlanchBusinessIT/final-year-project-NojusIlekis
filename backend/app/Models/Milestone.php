<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'category', 'age_range_months', 'sort_order',
    ];

    public const CATEGORIES = [
        'wellbeing' => 'Well-being',
        'identity-belonging' => 'Identity & Belonging',
        'communicating' => 'Communicating',
        'exploring-thinking' => 'Exploring & Thinking',
    ];

    public const AGE_RANGES = [
        '0-12' => 'Birth to 12 months',
        '12-24' => '12 to 24 months',
        '24-36' => '24 to 36 months',
        '36-48' => '3 to 4 years',
        '48-60' => '4 to 5 years',
    ];

    public function children()
    {
        return $this->belongsToMany(Child::class, 'child_milestones')
                     ->withPivot(['observed_by', 'observed_at', 'notes'])
                     ->withTimestamps();
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByAgeRange($query, string $ageRange)
    {
        return $query->where('age_range_months', $ageRange);
    }
}
