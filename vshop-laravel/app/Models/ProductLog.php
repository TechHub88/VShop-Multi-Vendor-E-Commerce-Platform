<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLog extends Model
{
    use HasFactory;

    protected $table = 'product_logs';

    protected $fillable = [
        'product_id',
        'module',
        'old_value',
        'new_value',
        'action_by',
        'action_type',
        'ip_address',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    /**
     * Relation: Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relation: Actor (Seller/Admin)
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
