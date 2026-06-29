<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $table = 'wallet_transactions';
    
    protected $fillable = [
        'wallet_id', 'seller_id', 'customer_id', 'user_type', 'transaction_type',
        'credit', 'debit', 'balance_after', 'description', 'reference_id',
        'metadata', 'status'
    ];
    
    protected $casts = [
        'credit' => 'decimal:2',
        'debit' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];
    
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
    
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
    
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    
    public function getUserAttribute()
    {
        if ($this->user_type === 'seller') {
            return $this->seller;
        }
        return $this->customer;
    }
    
    public function getUserNameAttribute()
    {
        $user = $this->user;
        return $user ? $user->name : 'N/A';
    }
    
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    public function scopeOfType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function getTypeAttribute(): string
    {
        return $this->credit > 0 ? 'credit' : 'debit';
    }

    public function getAmountAttribute(): float
    {
        return $this->credit > 0 ? (float)$this->credit : (float)$this->debit;
    }
}