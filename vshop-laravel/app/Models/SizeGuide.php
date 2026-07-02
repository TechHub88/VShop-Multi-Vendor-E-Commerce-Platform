<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeGuide extends Model
{
    use HasFactory;

    // Table name (optional if following Laravel naming convention)
    protected $table = 'size_guides';

    // Primary key (optional if 'id')
    protected $primaryKey = 'id';

    // Fillable fields
    protected $fillable = [
        'category_id',
        'size_label',
        'chest_in',
        'chest_cm',
        'front_length_in',
        'front_length_cm',
        'sleeve_length_in',
        'sleeve_length_cm',
        'created_at',
        'updated_at',
    ];

    // Disable timestamps if you manually handle them (optional)
    // public $timestamps = true;

    /**
     * Optional: Define relationship to Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
