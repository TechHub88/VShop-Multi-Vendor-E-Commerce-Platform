<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerCampaignParticipation extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'product_id',
        'advertisement_id',
        'campaign_type',
        'combo_details',
        'status',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
