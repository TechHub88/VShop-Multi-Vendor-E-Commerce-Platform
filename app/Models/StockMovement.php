<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity_changed',
        'stock_before',
        'stock_after',
        'reference',
        'reason',
        'user_name',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
