<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerBlacklist extends Model
{
    protected $table = 'seller_blacklists';
    
    protected $fillable = [
        'seller_id', 'product_id', 'customer_id', 'blocked_by', 'block_type',
        'reason', 'duration', 'block_until', 'status', 'metadata',
        'unblocked_at', 'unblocked_by', 'unblock_reason'
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'block_until' => 'datetime',
        'unblocked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Relationships
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    
    public function blockedBy()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }
    
    public function unblockedBy()
    {
        return $this->belongsTo(User::class, 'unblocked_by');
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopeBlockType($query, $type)
    {
        return $query->where('block_type', $type);
    }
    
    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }
    
    public function getIsTemporaryAttribute()
    {
        return $this->duration === 'temp' && $this->block_until && $this->block_until > now();
    }
    
    public function getBlockedItemNameAttribute()
    {
        switch($this->block_type) {
            case 'seller':
                return $this->seller ? ($this->seller->org_name ?? $this->seller->name) : 'Deleted Seller';
            case 'product':
                return $this->product ? $this->product->name : 'Deleted Product';
            case 'customer':
                return $this->customer ? $this->customer->name : 'Deleted Customer';
            default:
                return 'Unknown';
        }
    }
    
    public function getBlockedItemEmailAttribute()
    {
        switch($this->block_type) {
            case 'seller':
                return $this->seller ? $this->seller->email : null;
            case 'customer':
                return $this->customer ? $this->customer->email : null;
            default:
                return null;
        }
    }
}