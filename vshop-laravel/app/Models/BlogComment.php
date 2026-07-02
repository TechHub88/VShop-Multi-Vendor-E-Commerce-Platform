<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    use HasFactory;

    protected $table = 'blog_comments';

    protected $fillable = [
        'blog_id',
        'name',
        'email',
        'website',
        'comment',
        'status',
        'parent_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the blog that owns the comment.
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Get the parent comment.
     */
    public function parent()
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    /**
     * Get the replies to this comment.
     */
    public function replies()
    {
        return $this->hasMany(BlogComment::class, 'parent_id')->where('status', 'active');
    }

    /**
     * Scope a query to only include active comments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
