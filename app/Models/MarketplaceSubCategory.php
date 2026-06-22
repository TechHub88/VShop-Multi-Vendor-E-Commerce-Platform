<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceSubCategory extends Model
{
    protected $table = 'marketplace_sub_categories';

    protected $fillable = [
        'category_id',
        'name',
        'status','seller_id'
    ];

    public $timestamps = false;
}