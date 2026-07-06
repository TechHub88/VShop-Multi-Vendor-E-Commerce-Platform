<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerCommission extends Model
{
    protected $fillable=[
        'seller_id',
        'order_id',
        'product_service',
        'category',
        'fulfillment_mode',
        'order_amount',
        'commission_rate',
        'commission_amount',
        'logistic_fee',
        'payment_gateway_fee',
        'service_fee',
        'tax_amount',
        'promotion_discount',
        'net_earning',
        'reconciliation_status'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
}
