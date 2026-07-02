<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicPage extends Model
{
    protected $table = 'dynamic_pages';

    protected $fillable = [
        'title', 'slug', 'content', 'excerpt', 'featured_image', 'status',
        'page_type', 'template', 'app_type',
        'seo_title', 'seo_description', 'seo_keywords', 'canonical_url', 'robots',
        'og_title', 'og_description', 'og_image',
        'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image',
        'schema_markup', 'sections', 'blocks',
        'travel_price', 'travel_duration', 'travel_inclusions', 'travel_exclusions',
        'mapped_sellers', 'mapped_categories', 'mapped_products',
        'cta_text', 'cta_link', 'cta_position',
        'secondary_cta_text', 'secondary_cta_link',
        'analytics_id', 'track_conversions', 'conversion_goals',
        'enable_ai_blocks', 'ai_recommendation_type',
        'auto_internal_linking', 'enable_smart_suggestions',
        'is_featured', 'is_sponsored', 'sponsored_by', 'sponsored_until', 'featured_order',
        'approved_by', 'approved_at', 'published_by', 'published_at',
        'rejected_by', 'rejected_at', 'rejection_reason',
        'submitted_by', 'submitted_at', 'scheduled_publish_at',
        'views', 'unique_visitors', 'avg_time_on_page', 'conversion_rate', 'revenue',
        'meta', 'settings', 'created_by', 'updated_by', 'seller_id'
    ];

    protected $casts = [
        'mapped_sellers' => 'array',
        'mapped_categories' => 'array',
        'mapped_products' => 'array',
        'conversion_goals' => 'array',
        'sections' => 'array',
        'blocks' => 'array',
        'meta' => 'array',
        'settings' => 'array',
        'track_conversions' => 'boolean',
        'enable_ai_blocks' => 'boolean',
        'auto_internal_linking' => 'boolean',
        'enable_smart_suggestions' => 'boolean',
        'is_featured' => 'boolean',
        'is_sponsored' => 'boolean',
        'scheduled_publish_at' => 'datetime',
        'published_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function creator()
    {
        return $this->belongsTo(Seller::class, 'created_by');
    }

    public function images()
    {
        return $this->hasMany(PageImage::class, 'page_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function($q) {
                $q->whereNull('scheduled_publish_at')
                  ->orWhere('scheduled_publish_at', '<=', now());
            });
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Helper methods
    public function getPrimaryCta()
    {
        return (object) [
            'text' => $this->cta_text,
            'url' => $this->cta_link,
            'position' => $this->cta_position
        ];
    }

    public function getSecondaryCta()
    {
        return (object) [
            'text' => $this->secondary_cta_text,
            'url' => $this->secondary_cta_link
        ];
    }
}