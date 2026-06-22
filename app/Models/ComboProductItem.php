<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboProductItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'combo_id',
        'product_id',
        'quantity',
        'price',
        'color_id',
        'size_id'
    ];

    // Relationship with main product (product_id)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Relationship with combo product (combo_id contains Product ID)
    public function comboProduct()
    {
        return $this->belongsTo(Product::class, 'combo_id');
    }

    // Relationship with combo (legacy - combo_id now contains Product ID, not ComboProduct ID)
    public function combo()
    {
        return $this->belongsTo(ComboProduct::class, 'combo_id');
    }
}