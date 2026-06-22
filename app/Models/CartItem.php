<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    // In CartItem model
protected $fillable = [
    'product_id',
    'combo_id',
 
    'color_id',
    'size_id',
    'session_id',
    'customer_id',
    'is_combo_item',
    'combo_item_color_id',
    'combo_item_size_id',
    'main_product_attributes',      // JSON: Main product attributes
    'combo_product_attributes',     // JSON: Combo product attributes
    'main_product_quantity',        // Main product quantity
    'main_product_price',           // Main product price
    'combo_product_quantity',       // Combo product quantity
    'combo_product_price',   
    'coupon_id',
    'coupon_code',
    'discount_amount',
    'discount_type',// Combo product price
    'embroidery_logo',
    'embroidery_price',
    'embroidery_total_price'
];

protected $casts = [
'service_days' => 'array',
];

// Relationships
public function product()
{
    return $this->belongsTo(Product::class);
}

// Removed combo relationship since we're not using ComboProduct model anymore
// combo_id is now used as a string identifier, not a foreign key
// public function combo()
// {
//     return $this->belongsTo(ComboProduct::class, 'combo_id');
// }


public function color() {
    return $this->belongsTo(AttributeTerm::class, 'color_id');
}

public function size() {
    return $this->belongsTo(AttributeTerm::class, 'size_id');
}

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * Get the sizes for this cart item (multiple sizes with quantities).
     */
    public function sizes()
    {
        return $this->hasMany(CartItemSize::class);
    }

}
