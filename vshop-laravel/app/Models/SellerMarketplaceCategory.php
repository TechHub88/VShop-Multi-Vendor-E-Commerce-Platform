<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerMarketplaceCategory extends Model
{
    protected $fillable = [
        'seller_id',
        'category_id',
        'sub_category_id'
    ];
    
    public function category()
{
    return $this->belongsTo(\App\Models\Category::class, 'category_id');
}
}
