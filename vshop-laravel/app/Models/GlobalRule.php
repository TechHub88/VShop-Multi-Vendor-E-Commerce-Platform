<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GlobalRule extends Model
{
    use HasFactory;

    protected $table = 'global_rules';

    protected $fillable = [
        'seller_id',
        'app',
        'sell_type',
        'category_id',
        'product_id',
        'currency',
        'amount',
        'commission',
        'discount',
        'min_margin',
        'boost',
        'effective_date','app','commission','discount','min_margin'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'discount' => 'decimal:2',
        'min_margin' => 'decimal:2',
        'boost' => 'boolean',
        'effective_date' => 'date'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function category()
    {
        return $this->belongsTo(MarketplaceCategory::class, 'category_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
