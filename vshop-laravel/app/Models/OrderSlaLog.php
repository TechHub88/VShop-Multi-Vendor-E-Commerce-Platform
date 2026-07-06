<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSlaLog extends Model
{
    protected $fillable = [
        'order_id',
        'status'
    ];
}
