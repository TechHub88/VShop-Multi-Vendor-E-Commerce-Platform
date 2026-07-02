<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'author_id',
        'category_id',
        'featured_image',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'tags',
        'status',
        'is_featured',
        'view_count',
        'published_at',
        'seo_data'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'seo_data' => 'array',
        'tags' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
            
            // Ensure unique slug
            $originalSlug = $blog->slug;
            $count = 1;
            while (static::where('slug', $blog->slug)->exists()) {
                $blog->slug = $originalSlug . '-' . $count++;
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title')) {
                $blog->slug = Str::slug($blog->title);
                
                // Ensure unique slug
                $originalSlug = $blog->slug;
                $count = 1;
                while (static::where('slug', $blog->slug)
                    ->where('id', '!=', $blog->id)
                    ->exists()) {
                    $blog->slug = $originalSlug . '-' . $count++;
                }
            }
        });
    }
    
    public function category()
{
    return $this->belongsTo(BlogCategory::class, 'category_id');
}

    // Relationship with author
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Relationship with comments
    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id');
    }

    // Relationship with active comments only
    public function activeComments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id')->where('status', 'active')->whereNull('parent_id');
    }

    // Scope for published blogs
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where('published_at', '<=', Carbon::now());
    }

    // Scope for featured blogs
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                     ->published();
    }

    // Scope by category
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category)
                     ->published();
    }

    // Get formatted date
    public function getFormattedDateAttribute()
    {
        return $this->published_at->format('F j, Y');
    }

    // Get reading time
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200); // Average reading speed: 200 words per minute
        return $minutes . ' min read';
    }

    // Check if published
    public function getIsPublishedAttribute()
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at->lte(Carbon::now());
    }
}