<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VshopBanner extends Model
{
    protected $table = 'vshopbanners';

 protected $fillable = [
    'category_id',
    'banner',
    'title',
    'type',
    'platform',
    'start_date',
    'end_date',
    'description',
    'status'
];

    public function category()
    {
        return $this->belongsTo(MarketplaceCategory::class, 'category_id');
    }
}