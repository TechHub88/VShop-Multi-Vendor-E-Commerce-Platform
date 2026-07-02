<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'user_name',
        'user_email',
        'rating',
        'title',
        'comment',
        'is_approved',
        'is_verified_purchase',
        'would_recommend',
        'helpful_yes',
        'helpful_no',
        'admin_reply',
        'replied_at',
        'replied_by',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'is_verified_purchase' => 'boolean',
        'would_recommend' => 'boolean',
        'helpful_yes' => 'integer',
        'helpful_no' => 'integer',
        'replied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the review.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the customer that wrote the review.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the admin who replied to the review.
     */
    public function adminReplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Check if review has admin reply.
     */
    public function hasAdminReply(): bool
    {
        return !empty($this->admin_reply);
    }
}