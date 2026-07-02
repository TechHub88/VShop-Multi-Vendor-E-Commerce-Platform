<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingTool extends Model
{
    use HasFactory;

    // If table name is not the plural of model, you can explicitly set it
    protected $table = 'tracking_tools';

    // Fields that can be mass assigned
    protected $fillable = [
        'title',
        'code',
        'description'
    ];

    // Optionally cast fields
    protected $casts = [
        'title' => 'string',
        'code' => 'string',
        'description' => 'string',
    ];
}
