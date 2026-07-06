<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerWebsiteTemplate extends Model
{
    protected $fillable = [
        'seller_id',
        'template_id'
    ];

    /* =========================
       TEMPLATE RELATION
    ========================= */

    public function template()
    {
        return $this->belongsTo(
            WebsiteTemplate::class,   // 👈 template table model
            'template_id'
        );
    }

    /* =========================
       SELLER RELATION (optional)
    ========================= */

    public function seller()
    {
        return $this->belongsTo(Seller::class,'seller_id');
    }
}
