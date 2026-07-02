<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerDispute extends Model
{
    protected $fillable = [
        'seller_id',
        'order_id',
        'message',
        'status',
        'admin_reply'
    ];
}