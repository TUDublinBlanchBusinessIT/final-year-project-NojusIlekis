<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'child_id',
        'parent_id',
        'period_start',
        'period_end',
        'due_date',
        'total',
        'discount',
        'status',
        'payment_status', 'payment_proof_path', 'payment_submitted_at',
        'payment_approved_at', 'payment_notes', 'rejection_reason', 'payment_approved_by',
    ];

    protected $casts = [
        'payment_submitted_at' => 'datetime',
        'payment_approved_at' => 'datetime',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_approved_by');
    }

    /**
     * Can the parent submit payment for this invoice?
     */
    public function canSubmitPayment(): bool
    {
        return $this->status === 'sent' && in_array($this->payment_status, ['unpaid', 'rejected']);
    }

    /**
     * Is payment waiting for manager approval?
     */
    public function isPaymentPending(): bool
    {
        return $this->payment_status === 'payment_submitted';
    }

    /**
     * Mark payment as submitted by parent.
     */
    public function markPaymentSubmitted(string $proofPath, ?string $notes = null): void
    {
        $this->update([
            'payment_status' => 'payment_submitted',
            'payment_proof_path' => $proofPath,
            'payment_submitted_at' => now(),
            'payment_notes' => $notes,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Approve the payment (manager action).
     */
    public function approvePayment(int $approvedById): void
    {
        $this->update([
            'status' => 'paid',
            'payment_status' => 'approved',
            'payment_approved_at' => now(),
            'payment_approved_by' => $approvedById,
        ]);
    }

    /**
     * Reject the payment (manager action).
     */
    public function rejectPayment(string $reason): void
    {
        $this->update([
            'payment_status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Cancel/void this invoice (manager action).
     */
    public function cancelInvoice(): void
    {
        $this->update([
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
        ]);
    }

    /**
     * Can this invoice be edited?
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'sent']) && $this->payment_status === 'unpaid';
    }

    /**
     * Can this invoice be cancelled?
     */
    public function canBeCancelled(): bool
    {
        return $this->status !== 'cancelled' && $this->status !== 'paid';
    }

    /**
     * Get a colour class for the payment status badge.
     */
    public function paymentStatusColour(): string
    {
        return match($this->payment_status) {
            'unpaid' => 'bg-gray-100 text-gray-700',
            'payment_submitted' => 'bg-amber-100 text-amber-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get a label for the payment status.
     */
    public function paymentStatusLabel(): string
    {
        return match($this->payment_status) {
            'unpaid' => 'Unpaid',
            'payment_submitted' => 'Payment Submitted',
            'approved' => 'Paid',
            'rejected' => 'Payment Rejected',
            default => 'Unknown',
        };
    }
}