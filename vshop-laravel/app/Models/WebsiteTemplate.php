<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteTemplate extends Model
{
    protected $fillable = [
        'name',
        'application_type',
        'preview_image',
        'description',
        'status','seller_id'
    ];
}
