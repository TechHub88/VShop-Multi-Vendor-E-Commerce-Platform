<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
         protected $fillable = [
        'order_id', 
        'product_id', 
        'combo_id',
        'combo_item_color_id',
        'combo_item_size_id',
        'color_id', 
        'size_id', 
        'quantity', 
        'price', 
        'total',
        'main_product_attributes',
        'combo_product_attributes',
        'main_product_quantity',
        'main_product_price',
        'combo_product_quantity',
        'combo_product_price',
        'is_combo_item',
        'gst_percent',
        'courier_charge','commission_amount','seller_earning','commission_percent','referral_earning','platform_earning'
    ];

    // Relation to Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    // Relation to Color (ProductAttribute Term)
   // App/Models/OrderItem.php
 public function color()
    {
        return $this->belongsTo(AttributeTerm::class, 'color_id');
    }

    public function size()
    {
        return $this->belongsTo(AttributeTerm::class, 'size_id');
    }

    // Relation to Order
public function order()
{
    return $this->belongsTo(Order::class, 'order_id', 'id');
}

}
