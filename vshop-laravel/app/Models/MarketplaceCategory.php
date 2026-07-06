<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceCategory extends Model
{
    protected $fillable = [
        'name',
        'status',
        'seller_id',
        'type'
    ];

    /**
     * Relationship: A category has many subcategories
     */
    public function subCategories()
    {
        return $this->hasMany(MarketplaceSubCategory::class, 'category_id');
    }

    /**
     * Relationship: A category belongs to a seller
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    /**
     * Accessor: Convert integer status to approval string
     * 
     * @return string
     */
    public function getApprovalStatusAttribute()
    {
        return match ((int) $this->status) {
            1 => 'approved',
            2 => 'rejected',
            default => 'pending',
        };
    }

    /**
     * Mutator: Convert approval string to integer status
     * 
     * @param string $value
     * @return void
     */
    public function setApprovalStatusAttribute($value)
    {
        $this->status = match ($value) {
            'approved' => 1,
            'rejected' => 2,
            default => 0,
        };
    }
}
