<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPerformance extends Model
{
    protected $table = 'supplier_performance'; // 🔥 FIX

    protected $fillable = [
        'supplier_name',
        'success_rate',
        'avg_delivery_days'
    ];
}
