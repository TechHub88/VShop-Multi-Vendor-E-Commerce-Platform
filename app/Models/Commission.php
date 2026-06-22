<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $table = 'commissions';

    protected $fillable = [
        'seller_id',
        'order_id',
        'product_service',
        'order_amount',
        'commission_amount',
        'logistic_fee',
        'payment_gateway_fee',
        'service_fee',
        'net_earning',
        'reconciliation_status'
    ];

    protected $casts = [
        'order_amount' => 'float',
        'commission_amount' => 'float',
        'logistic_fee' => 'float',
        'payment_gateway_fee' => 'float',
        'service_fee' => 'float',
        'net_earning' => 'float',
    ];

    // ================= RELATIONS =================

    public function seller()
    {
        return $this->belongsTo(\App\Models\User::class, 'seller_id');
    }

    // ================= ACCESSORS =================

    public function getTotalFeesAttribute()
    {
        return $this->logistic_fee +
               $this->payment_gateway_fee +
               $this->service_fee;
    }

    public function getFormattedNetAttribute()
    {
        return number_format($this->net_earning, 2);
    }

    // ================= SCOPES =================

    public function scopeSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
}