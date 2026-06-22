<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierIntegration extends Model
{
    protected $fillable = [
        'seller_id',
        'carrier_name',
        'status',
        'sla_defined'
    ];
}