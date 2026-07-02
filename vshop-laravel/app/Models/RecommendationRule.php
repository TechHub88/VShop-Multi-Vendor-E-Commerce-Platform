<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationRule extends Model
{
    protected $table = 'recommendation_rules';

    protected $fillable = [
        'type',
        'source',
        'source_type',
        'targets',
        'priority',
        'is_active'
    ];

    protected $casts = [
        'targets' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer'
    ];

    // Scope for active rules
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    // Scope by type
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessor to get targets as array
    public function getTargetsArrayAttribute()
    {
        return is_array($this->targets) ? $this->targets : json_decode($this->targets, true);
    }

    // Mutator to ensure targets are stored as JSON
    public function setTargetsAttribute($value)
    {
        $this->attributes['targets'] = is_array($value) ? json_encode($value) : $value;
    }
}
