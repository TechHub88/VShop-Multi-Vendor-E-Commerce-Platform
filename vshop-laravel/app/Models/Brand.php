<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file',
        'type',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'website',
        'contact_person',
        'description',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    /**
     * Relationship with products using brand_id column
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }

    /**
     * Relationship with branches
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Relationship with institution details
     */
    public function institutionDetails()
    {
        return $this->hasMany(InstitutionDetail::class);
    }

    /**
     * Scope for locations (travel admin)
     */
    public function scopeLocations($query)
    {
        return $query->where('type', 'location');
    }

    /**
     * Scope for brands (product admin)
     */
    public function scopeBrands($query)
    {
        return $query->where('type', 'brand');
    }

    /**
     * Check if this is a location
     */
    public function isLocation()
    {
        return $this->type === 'location';
    }

    /**
     * Check if this is a brand
     */
    public function isBrand()
    {
        return $this->type === 'brand';
    }
}
