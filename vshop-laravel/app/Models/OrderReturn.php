<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    // Return type constants
    const TYPE_RETURN = 'return';
    const TYPE_REPLACEMENT = 'replacement';

    // Status constants (if you use them)
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'customer_id',
        'reason',
        'note',
        'status',
        'refund_mode',
        'pickup_date',
        'pickup_time',
        'return_type',
        'otp','attachment',
        'escalated_to_admin_at',
        'seller_action_deadline',
        'pickup_initiated_at',
        'admin_refund_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pickup_date' => 'date:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order that owns the return.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the customer that requested the return.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Scope a query to only include returns of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('return_type', $type);
    }

    /**
     * Scope a query to only include returns with a specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Determine if the return is a replacement.
     */
    public function isReplacement(): bool
    {
        return $this->return_type === self::TYPE_REPLACEMENT;
    }

    /**
     * Determine if the return is a standard return.
     */
    public function isReturn(): bool
    {
        return $this->return_type === self::TYPE_RETURN;
    }
}
