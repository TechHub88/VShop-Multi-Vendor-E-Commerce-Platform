<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerFinance extends Model
{
    protected $fillable = [
        'seller_id',
        'order_id',
        'transaction_type',
        'reference_no',
        'credit',
        'debit',
        'balance',
        'payout_method',
        'utr_number',
        'tax_amount',
        'commission_amount',
        'status',
        'note',
        'dispute_status',
        'settlement_cycle','release_date'
    ];

    // Add this relationship to fix the error
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id', 'id');
    }
}
