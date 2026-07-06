<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletFrozenLog extends Model
{
    protected $table = 'wallet_frozen_logs';
    
    protected $fillable = [
        'wallet_id',
        'action',
        'reason',
        'performed_by'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the wallet associated with this log
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }
    
    /**
     * Get the user who performed the action
     */
    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
    
    /**
     * Scope for freeze actions only
     */
    public function scopeFreezes($query)
    {
        return $query->where('action', 'freeze');
    }
    
    /**
     * Scope for unfreeze actions only
     */
    public function scopeUnfreezes($query)
    {
        return $query->where('action', 'unfreeze');
    }
    
    /**
     * Scope for specific wallet
     */
    public function scopeForWallet($query, $walletId)
    {
        return $query->where('wallet_id', $walletId);
    }
    
    /**
     * Get action badge color
     */
    public function getActionBadgeColorAttribute(): string
    {
        return $this->action === 'freeze' ? 'danger' : 'success';
    }
    
    /**
     * Get action icon
     */
    public function getActionIconAttribute(): string
    {
        return $this->action === 'freeze' ? 'mdi-snowflake' : 'mdi-lock-open-variant';
    }
    
    /**
     * Get formatted action text
     */
    public function getFormattedActionAttribute(): string
    {
        return ucfirst($this->action);
    }
    
    /**
     * Get performer name
     */
    public function getPerformerNameAttribute(): string
    {
        return $this->performer ? $this->performer->name : 'System';
    }
    
    /**
     * Get wallet user name
     */
    public function getWalletUserNameAttribute(): string
    {
        if (!$this->wallet) {
            return 'N/A';
        }
        
        if ($this->wallet->user_type === 'seller' && $this->wallet->seller_id) {
            $seller = Seller::find($this->wallet->seller_id);
            return $seller ? $seller->name : 'Seller #' . $this->wallet->seller_id;
        } elseif ($this->wallet->customer_id) {
            $customer = User::find($this->wallet->customer_id);
            return $customer ? $customer->name : 'Customer #' . $this->wallet->customer_id;
        }
        
        return 'Unknown User';
    }
    
    /**
     * Get wallet user type
     */
    public function getWalletUserTypeAttribute(): string
    {
        return $this->wallet ? $this->wallet->user_type : 'N/A';
    }
}
