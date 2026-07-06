<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = [
        'seller_id',
        'zone_name',
        'base_price',
        'condition_text'
    ];
}
