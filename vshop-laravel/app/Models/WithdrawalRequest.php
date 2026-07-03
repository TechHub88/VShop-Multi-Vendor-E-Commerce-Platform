<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    protected $table = 'withdrawal_requests';
    
    protected $fillable = [
        'wallet_id', 'seller_id', 'customer_id', 'user_type', 'amount',
        'request_id', 'bank_account_name', 'bank_name', 'bank_account_number',
        'bank_ifsc', 'remarks', 'admin_remarks', 'status', 'processed_by',
        'processed_at', 'transaction_id'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->request_id)) {
                $model->request_id = 'WDR' . time() . rand(1000, 9999);
            }
        });
    }
    
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
    
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
    
    public function approve($adminId, $remarks = null): bool
    {
        $this->status = 'approved';
        $this->processed_by = $adminId;
        $this->processed_at = now();
        $this->admin_remarks = $remarks;
        
        return $this->save();
    }
    
    public function reject($adminId, $remarks = null): bool
    {
        // Return the frozen amount back to balance
        $wallet = $this->wallet;
        if ($wallet) {
            $wallet->frozen_balance -= $this->amount;
            $wallet->save();
        }
        
        $this->status = 'rejected';
        $this->processed_by = $adminId;
        $this->processed_at = now();
        $this->admin_remarks = $remarks;
        
        return $this->save();
    }
}