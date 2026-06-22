<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'reference_no',
        'transaction_type',
        'credit',
        'debit',
        'tax',
        'status'
    ];
}