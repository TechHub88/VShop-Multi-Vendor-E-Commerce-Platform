<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InternalSupport extends Model
{
    protected $fillable = [
        'conversation_id',
        'ticket_number',
        'department',
        'priority',
        'subject',
        'description',
        'assigned_to',
        'created_by',
        'status',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeSupport($query)
    {
        return $query->where('department', 'support');
    }

    public function scopeFinance($query)
    {
        return $query->where('department', 'finance');
    }

    public function scopeTechnical($query)
    {
        return $query->where('department', 'technical');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function markAsResolved(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    public static function generateTicketNumber(): string
    {
        $prefix = 'TCK';
        $timestamp = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
        return "{$prefix}-{$timestamp}-{$random}";
    }
}
