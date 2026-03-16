<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Acknowledgement extends Model
{
    protected $fillable = [
        'record_type',
        'record_id',
        'parent_id',
        'status',
        'signed_at',
        'signature_name',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}