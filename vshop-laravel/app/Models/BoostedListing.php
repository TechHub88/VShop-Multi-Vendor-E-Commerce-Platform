<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoostedListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'seller_id', 'category_id', 'per_day', 'boost_amount', 'start_date', 'end_date',
        'status', 'impressions', 'clicks', 'enquiries', 'conversions',
        'revenue_generated'
    ];

    protected $casts = [
        'per_day' => 'decimal:2',
        'boost_amount' => 'decimal:2',
        'revenue_generated' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function category()
    {
        return $this->belongsTo(MarketplaceCategory::class, 'category_id');
    }
}
