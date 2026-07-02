<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grievance extends Model
{
    protected $table = 'grievances';

    protected $fillable = [
        'seller_id',
        'buyer_id',
        'order_id',
        'type',
        'subject',
        'message',
        'priority',
        'status',
        'assigned_to',
        'escalated_to',
        'escalated_at',
        'time_frame',
        'escalation_level',
        'is_auto_escalated',
        'complaint_number',  // <-- ADD
        'attachments',       // <-- ADD
        'assigned_at',       // <-- ADD
        'investigation_started_at', // <-- ADD
        'resolved_at',       // <-- ADD
    ];
    protected $casts = [
        'escalated_at' => 'datetime',
        'is_auto_escalated' => 'boolean'
    ];

    /* ================= RELATIONS ================= */

    public function messages()
    {
        return $this->hasMany(GrievanceMessage::class, 'grievance_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /* ================= SCOPES ================= */

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeNeedEscalation($query)
    {
        return $query->where('status', 'open')
                     ->where('is_auto_escalated', false);
    }

    /* ================= HELPERS ================= */

    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function priorityBadge()
    {
        return match($this->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            default => 'secondary'
        };
    }

    public function statusBadge()
    {
        return match($this->status) {
            'open' => 'danger',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'dark'
        };
    }

    // Auto Escalation Helper Methods
    public function escalateTo($team, $timeFrame = '24 Hours')
    {
        $this->update([
            'escalated_to' => $team,
            'escalated_at' => now(),
            'time_frame' => $timeFrame,
            'escalation_level' => $this->escalation_level + 1,
            'is_auto_escalated' => true,
            'assigned_to' => $team
        ]);
    }

    public function resolveEscalation()
    {
        $this->update([
            'status' => 'resolved',
            'is_auto_escalated' => false,
            'escalated_at' => null
        ]);
    }

    public function getPreviousAssignedAttribute()
    {
        return $this->assigned_to ?? 'Not Assigned';
    }

    public function getEscalationHistoryAttribute()
    {
        return [
            'level' => $this->escalation_level,
            'to' => $this->escalated_to,
            'at' => $this->escalated_at,
            'time_frame' => $this->time_frame
        ];
    }
}