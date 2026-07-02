<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $table = 'refunds';
    
    protected $fillable = [
        'order_id',
        'customer_id',
        'amount',
        'reason',
        'status',
        'processed_by',
        'processed_at'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the customer who requested the refund
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    /**
     * Get the admin who processed the refund
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
    
    /**
     * Scope for pending refunds
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    /**
     * Scope for approved refunds
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    
    /**
     * Scope for rejected refunds
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
    
    /**
     * Scope for refunded refunds
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }
    
    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'refunded' => 'success'
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }
    
    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }
    
    /**
     * Get reason color for badge
     */
    public function getReasonColorAttribute(): string
    {
        $colors = [
            'Defective' => 'danger',
            'Defective Product' => 'danger',
            'Damaged' => 'danger',
            'Wrong Item' => 'warning',
            'Wrong Size' => 'warning',
            'Order Cancelled' => 'info',
            'Cancelled' => 'info',
            'Shipping Delay' => 'info',
            'Not Delivered' => 'warning',
            'Quality Issue' => 'danger',
            'Delivery Issue' => 'warning'
        ];
        
        foreach ($colors as $key => $color) {
            if (stripos($this->reason, $key) !== false) {
                return $color;
            }
        }
        
        return 'secondary';
    }
    
    /**
     * Approve the refund
     */
    public function approve($processedBy = null): bool
    {
        $this->status = 'approved';
        $this->processed_by = $processedBy ?? auth()->id();
        $this->processed_at = now();
        
        return $this->save();
    }
    
    /**
     * Reject the refund
     */
    public function reject($processedBy = null): bool
    {
        $this->status = 'rejected';
        $this->processed_by = $processedBy ?? auth()->id();
        $this->processed_at = now();
        
        return $this->save();
    }
    
    /**
     * Mark as refunded (money sent)
     */
    public function markAsRefunded($processedBy = null): bool
    {
        $this->status = 'refunded';
        $this->processed_by = $processedBy ?? auth()->id();
        $this->processed_at = now();
        
        return $this->save();
    }
    
    /**
     * Check if refund is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    
    /**
     * Check if refund is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
    
    /**
     * Check if refund is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
    
    /**
     * Check if refund is completed
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }
    
    /**
     * Get formatted order ID
     */
    public function getFormattedOrderIdAttribute(): string
    {
        return $this->order_id ?? '#ORD-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get formatted customer ID
     */
    public function getFormattedCustomerIdAttribute(): string
    {
        return '#CUST-' . str_pad($this->customer_id, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get customer name
     */
    public function getCustomerNameAttribute(): string
    {
        return $this->customer ? $this->customer->name : 'Unknown Customer';
    }
    
    /**
     * Get processor name
     */
    public function getProcessorNameAttribute(): string
    {
        return $this->processor ? $this->processor->name : 'System';
    }
}