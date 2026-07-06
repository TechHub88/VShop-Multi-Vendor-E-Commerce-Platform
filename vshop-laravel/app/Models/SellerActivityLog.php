<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerActivityLog extends Model
{
    protected $table = 'seller_activity_logs';

    public $timestamps = false; // we use created_at manually

    protected $fillable = [
        'seller_id',
        'action',
        'module',
        'field',
        'old_value',
        'new_value',
        'created_at','module'
    ];
}
