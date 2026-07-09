<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCancel extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'reason',
        'note',
        'status',
        'cancelled_dates',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
}
