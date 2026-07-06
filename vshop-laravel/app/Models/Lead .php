<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $table = 'leads';
    
    protected $fillable = [
        'customer_id',
        'seller_id', 
        'product_id',
        'type',
        'ip_address',
        'phone',
        'email',
        'customer_name',
        'click_count',
        'charge_percentage',
        'charge_amount',
        'is_fraud',
        'fraud_reason',
        'is_refunded',
        'refunded_at',
        'created_at'
    ];
    
    protected $casts = [
        'click_count' => 'integer',
        'charge_percentage' => 'integer',
        'charge_amount' => 'decimal:2',
        'is_fraud' => 'boolean',
        'is_refunded' => 'boolean',
        'refunded_at' => 'datetime',
        'created_at' => 'datetime'
    ];
    
    protected $dates = [
        'created_at',
        'refunded_at'
    ];
    
    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    // Scopes
    public function scopeClicks($query)
    {
        return $query->where('type', 'click');
    }
    
    public function scopeEnquiries($query)
    {
        return $query->where('type', 'enquiry');
    }
    
    public function scopeFraud($query)
    {
        return $query->where('is_fraud', 1);
    }
    
    public function scopeValid($query)
    {
        return $query->where('is_fraud', 0);
    }
    
    public function scopeRefunded($query)
    {
        return $query->where('is_refunded', 1);
    }
    
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
    
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }
    
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }
    
    // Helper methods
    public function isClick(): bool
    {
        return $this->type === 'click';
    }
    
    public function isEnquiry(): bool
    {
        return $this->type === 'enquiry';
    }
    
    public function isFraudulent(): bool
    {
        return (bool) $this->is_fraud;
    }
    
    public function isRefunded(): bool
    {
        return (bool) $this->is_refunded;
    }
    
    public function getChargeAmountFormattedAttribute(): string
    {
        return '$' . number_format($this->charge_amount, 2);
    }
    
    public function getFraudReasonListAttribute(): array
    {
        return explode('; ', $this->fraud_reason ?? '');
    }
    
    // Accessors
    public function getChargePercentageTextAttribute(): string
    {
        return $this->charge_percentage . '%';
    }
    
    public function getTypeBadgeAttribute(): string
    {
        return $this->type === 'click' 
            ? '<span class="badge bg-primary">Click</span>'
            : '<span class="badge bg-success">Enquiry</span>';
    }
    
    public function getFraudBadgeAttribute(): string
    {
        return $this->is_fraud
            ? '<span class="badge bg-danger">Fraud</span>'
            : '<span class="badge bg-success">Valid</span>';
    }
}
