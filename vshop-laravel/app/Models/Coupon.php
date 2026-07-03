<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_code',
        'name',
        'coupon_amount',
        'description',
        'discount_type',
        'expiry_date',
        'start_date',
        'minimum_spend',
        'max_discount_amount',
        'status',
        'usage_limit',
        'per_user_limit',
        'used_count',
        'coupon_type',
        'type',
        'partner_id',
        'applicable_on',
        'applicable_ids',
        'apply_on',
        'stackable',
        'show_in_public'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'start_date' => 'date',
        'coupon_amount' => 'decimal:2',
        'minimum_spend' => 'decimal:2',
        'used_count' => 'integer',
        'usage_limit' => 'integer',
        'applicable_ids' => 'array',
    ];

    /**
     * Check if coupon is valid
     */
    public function isValid()
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expiry_date < now()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon can be applied to order amount
     */
    public function canApplyTo($orderAmount)
    {
        return $orderAmount >= $this->minimum_spend;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount($orderAmount)
    {
        if (!$this->isValid() || !$this->canApplyTo($orderAmount)) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return ($orderAmount * $this->coupon_amount) / 100;
        }

        return min($this->coupon_amount, $orderAmount);
    }
}

