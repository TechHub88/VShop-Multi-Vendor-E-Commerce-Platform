<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
       use HasFactory;

protected $fillable = [
    'name', 'slug', 'description', 'banner_image', 'parent_id',
    'meta_title', 'meta_description', 'meta_keywords',
    'featured', 'show_in_store', 'status','title','size_guides','size_chart_inch','size_chart_cm','size_guide_status','type','created_by_id','created_by'
];


public function sizeGuides()
{
    return $this->hasMany(SizeGuide::class);
}

public function subcategories()
{
    return $this->hasMany(SubCategory::class, 'category_id');
}

    /**
     * Self Relationship (Parent Category)
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Children / Subcategories
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * One Category → Many Products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
