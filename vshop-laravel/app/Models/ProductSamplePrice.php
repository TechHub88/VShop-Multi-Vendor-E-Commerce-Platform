<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSamplePrice extends Model
{
    use HasFactory;

    protected $table = 'product_sample_prices';

    protected $fillable = [
        'product_id',
        'name',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
