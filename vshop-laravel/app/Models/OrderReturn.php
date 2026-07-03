<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'reason',
        'note',
        'status',
        'attachment',
    ];

    // Relationship to Order
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Relationship to Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
