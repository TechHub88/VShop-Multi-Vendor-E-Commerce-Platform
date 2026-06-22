<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'referred_customer_id',
        'referral_code',
        'status',
        'reward_amount',
        'completed_at',
        'expires_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'reward_amount' => 'decimal:2'
    ];

    /**
     * The customer who made the referral.
     */
    public function referrer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * The customer who was referred.
     */
    public function referredCustomer()
    {
        return $this->belongsTo(Customer::class, 'referred_customer_id');
    }
}