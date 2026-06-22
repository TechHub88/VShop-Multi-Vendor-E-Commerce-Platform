<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Complaint extends Model
{
    protected $fillable = [
        'product_id',
        'seller_id',
        'customer_id',
        'message'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}