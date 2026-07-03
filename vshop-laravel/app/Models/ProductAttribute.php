<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ProductAttribute extends Model
{
     use HasFactory;


    protected $table = 'product_attribute'; // your table name
 protected $fillable = [

'proid',
'combination_key',
'attribute_id',
'attribute_term_id',

'price',
'regular_price',
'product_stock',
'product_unit',

'payment_type',
'is_discount',
'discount_type',
'discount_value',
'discount_start',
'discount_end',

'tax_name',
'tax_percentage',
'tax_amount',

'shipping_amount',
'net_total',
'discount_start',
'discount_end',

'status'
];

    // Relationship to product
    public function product()
    {
        return $this->belongsTo(Product::class, 'proid', 'id');
    }

    // Relationship to attribute
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

    // Relationship to attribute term
public function term()
{
    return $this->belongsTo(AttributeTerm::class, 'attribute_term_id');
}

// Color relation
    public function color()
    {
        return $this->belongsTo(AttributeTerm::class, 'color_id');
    }

    // Size relation
    public function size()
    {
        return $this->belongsTo(AttributeTerm::class, 'size_id');
    }
}
