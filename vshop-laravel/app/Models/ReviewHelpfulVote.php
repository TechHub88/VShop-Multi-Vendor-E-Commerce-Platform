<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewHelpfulVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'is_helpful','product_id'
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    /**
     * Get the review that owns the vote.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(ProductReview::class);
    }

    /**
     * Get the user that cast the vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}