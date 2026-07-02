<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

   protected $fillable=[

        'enquiry_no',
        'seller_id',
        'user_id',

        'user_name',
        'user_email',
        'user_mobile',

        'country',
        'state',

        'category',
        'subcategory',

        'product_service',
        'quantity',

        'source',
        'status',

        'next_followup_date',
        'next_followup_time',
        'followup_note','tags','message','followup_count','product_id','photo'
    ];

    public function salesRep()
    {
        return $this->belongsTo(SalesRep::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
    
    // app/Models/Enquiry.php
public function product()
{
    return $this->belongsTo(Product::class);
}
}
