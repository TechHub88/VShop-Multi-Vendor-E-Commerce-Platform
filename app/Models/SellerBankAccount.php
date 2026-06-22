<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerBankAccount extends Model
{
    protected $fillable = [

        'seller_id',
        'method',

        'account_name',
        'bank_name',
        'account_number',
        'ifsc_code',

        'upi_id',

        'cancel_cheque',

        'status',

        'is_primary'
    ];
}