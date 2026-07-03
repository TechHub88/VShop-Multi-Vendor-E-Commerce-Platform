<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItemSize extends Model
{
    protected $fillable = [
        'cart_item_id',
        'size_id',
        'size_name',
        'quantity',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get the cart item that owns this size entry.
     */
    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    /**
     * Get the size attribute term.
     */
    public function size()
    {
        return $this->belongsTo(AttributeTerm::class, 'size_id');
    }
}

