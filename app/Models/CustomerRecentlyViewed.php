<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRecentlyViewed extends Model
{
    protected $table = 'customer_recently_viewed';
    
    protected $fillable = [
        'customer_id',
        'product_id',
        'viewed_at'
    ];
    
    protected $casts = [
        'viewed_at' => 'datetime'
    ];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}