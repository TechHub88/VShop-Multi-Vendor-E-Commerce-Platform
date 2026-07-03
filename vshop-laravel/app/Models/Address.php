<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
   protected $fillable = [
        'customer_id',   // must be here
        'name',
        'email',
        'company_name',
        'gstin',
        'phone',
        'country',
        'state',
        'city',
        'address_line1',
        'pincode',
        'address_type','type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
