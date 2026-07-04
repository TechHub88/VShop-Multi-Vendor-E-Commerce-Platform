<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageImage extends Model
{
    protected $table = 'page_images';

    protected $fillable = [
        'page_id', 'image', 'alt_text', 'sort_order'
    ];

    /**
     * Get the page that owns the image.
     */
    public function page()
    {
        return $this->belongsTo(DynamicPage::class, 'page_id');
    }
}