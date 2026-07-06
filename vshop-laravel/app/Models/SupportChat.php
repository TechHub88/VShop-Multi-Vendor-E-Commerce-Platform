<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportChat extends Model
{
    protected $fillable = [
        'seller_id',
        'order_id',
        'message',
        'sender'
    ];
}
