<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $table = 'advertisements';

    protected $fillable = [
        'title',
        'type',
        'description',
        'redirect_url',
        'target_type',
        'product_id',
        'category_id',
        'category_name',
        'service_id',
        'start_date',
        'end_date',
        'is_active',
        'created_by'
    ];

    /**
     * RELATION: Advertisement has many media
     */
    public function media()
    {
        return $this->hasMany(AdvertisementMedia::class, 'advertisement_id');
    }

    /**
     * OPTIONAL: Category Relation
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\MarketplaceCategory::class, 'category_id');
    }

    /**
     * OPTIONAL: Product Relation
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    /**
     * OPTIONAL: Service Relation
     */
    public function service()
    {
        return $this->belongsTo(\App\Models\Service::class, 'service_id');
    }

    /**
     * SCOPE: Active Ads
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}