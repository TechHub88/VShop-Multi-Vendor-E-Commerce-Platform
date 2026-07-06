<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'status'
    ];

    /**
     * RELATION: Page has many images
     */
    public function images()
    {
        return $this->hasMany(PageImage::class, 'page_id');
    }
}
