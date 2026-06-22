<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $fillable = [
        'seller_id',
        'shipping_type',
        'pricing_type',
        'auto_delivery_calc',
        'tracking_mode'
    ];
}