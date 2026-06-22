<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $table = 'wallets';
    
    protected $fillable = [
        'seller_id', 'customer_id', 'user_type', 'balance', 
        'frozen_balance', 'is_frozen', 'frozen_at', 'freeze_reason'
    ];
    
    protected $casts = [
        'balance' => 'decimal:2',
        'frozen_balance' => 'decimal:2',
        'is_frozen' => 'boolean',
        'frozen_at' => 'datetime',
    ];
    
    // Relationship with Seller
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
    
    // Relationship with Customer (User)
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    
    // Get the user dynamically
    public function getUserAttribute()
    {
        if ($this->user_type === 'seller') {
            return $this->seller;
        }
        return $this->customer;
    }
    
    // Get user ID dynamically
    public function getUserIdAttribute()
    {
        if ($this->user_type === 'seller') {
            return $this->seller_id;
        }
        return $this->customer_id;
    }
    
    // Get user name dynamically
    public function getUserNameAttribute()
    {
        $user = $this->user;
        return $user ? $user->name : 'N/A';
    }
    
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
    
    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }
    
    public function getAvailableBalanceAttribute(): float
    {
        return $this->balance - $this->frozen_balance;
    }
    
    public function freeze($reason): bool
    {
        $this->is_frozen = true;
        $this->frozen_at = now();
        $this->freeze_reason = $reason;
        
        return $this->save();
    }
    
    public function unfreeze(): bool
    {
        $this->is_frozen = false;
        $this->frozen_at = null;
        $this->freeze_reason = null;
        
        return $this->save();
    }
    
    public function addBalance($amount, $description, $referenceId = null, $metadata = []): ?WalletTransaction
    {
        if ($amount <= 0) {
            return null;
        }
        
        $this->balance += $amount;
        $this->save();
        
        return $this->createTransaction($amount, 0, $description, 'manual_credit', $referenceId, $metadata);
    }
    
    public function deductBalance($amount, $description, $referenceId = null, $metadata = []): ?WalletTransaction
    {
        if ($amount <= 0 || $this->available_balance < $amount) {
            return null;
        }
        
        $this->balance -= $amount;
        $this->save();
        
        return $this->createTransaction(0, $amount, $description, 'manual_debit', $referenceId, $metadata);
    }
    
    private function createTransaction($credit, $debit, $description, $type, $referenceId, $metadata): WalletTransaction
    {
        return WalletTransaction::create([
            'wallet_id' => $this->id,
            'seller_id' => $this->user_type === 'seller' ? $this->seller_id : null,
            'customer_id' => $this->user_type === 'customer' ? $this->customer_id : null,
            'user_type' => $this->user_type,
            'transaction_type' => $type,
            'credit' => $credit,
            'debit' => $debit,
            'balance_after' => $this->balance,
            'description' => $description,
            'reference_id' => $referenceId,
            'metadata' => $metadata,
            'status' => 'completed',
        ]);
    }
    
    // Scope for sellers only
    public function scopeSellers($query)
    {
        return $query->where('user_type', 'seller');
    }
    
    // Scope for customers only
    public function scopeCustomers($query)
    {
        return $query->where('user_type', 'customer');
    }
}