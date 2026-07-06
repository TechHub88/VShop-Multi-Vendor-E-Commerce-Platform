<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku', 
        'price',
        'regular_price',
        'description',
        'main_product_id',
        'combo_products',
        'thumbnail',
        'images',
        'is_active',
        'status'
    ];

    protected $casts = [
        'combo_products' => 'array',
        'images' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationship with main product
    public function mainProduct()
    {
        return $this->belongsTo(Product::class, 'main_product_id');
    }

    // Relationship with combo items
    public function items()
    {
        return $this->hasMany(ComboProductItem::class, 'combo_id');
    }

    // Relationship with products in the combo
    public function products()
    {
        return $this->belongsToMany(Product::class, 'combo_product_items', 'combo_id', 'product_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    // Generate unique SKU for combo
    public static function generateSku()
    {
        do {
            $sku = 'COMBO-' . strtoupper(uniqid());
        } while (self::where('sku', $sku)->exists());

        return $sku;
    }
}
