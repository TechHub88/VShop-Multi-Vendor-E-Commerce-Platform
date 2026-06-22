<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    
    protected $fillable = [
        'banner_image',   // JSON array of banner images
        'second_image',   // JSON array of second section images
    ];

   
}
