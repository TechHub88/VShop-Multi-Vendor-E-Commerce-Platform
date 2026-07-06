<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVersionHistory extends Model
{
    use HasFactory;

    protected $table = 'product_version_histories';

    protected $fillable = [
        'product_id',
        'snapshot',
        'action',
        'change_data',
        'changed_by',
        'created_at'
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
        'change_data' => 'array',
        'snapshot' => 'array'
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
