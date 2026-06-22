<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerBanner extends Model
{
    protected $fillable = [
        'seller_id',
        'banner_image',
        'banner_title',
        'banner_sub_title',
        'status',
        'admin_remark'
    ];

    protected $casts = [
        'banner_image' => 'array' // 🔥 IMPORTANT
    ];
}