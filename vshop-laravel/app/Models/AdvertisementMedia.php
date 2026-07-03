<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertisementMedia extends Model
{
    use HasFactory;

    protected $table = 'advertisement_media';

    protected $fillable = [
        'advertisement_id',
        'media_type',
        'file_path',
        'thumbnail'
    ];

    /**
     * RELATION: Media belongs to Advertisement
     */
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class, 'advertisement_id');
    }
}