<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerSeo extends Model
{
    protected $table = 'seller_seos';

    protected $fillable = [
        'seller_id',
        'home_title',
        'meta_description',
        'meta_keywords',
        'search_hashtags'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relation : SEO belongs to Seller
    |--------------------------------------------------------------------------
    */

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
