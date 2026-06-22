<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
      protected $fillable = [

        // Quick Links
        'quick_home',
        'quick_about',
        'quick_faq',
        'quick_testimonials',
        'quick_blogs',

        // Company
        'company_privacy_policy',
        'company_return_policy',
        'company_terms',

        // Category
        'category_corporate',
        'category_work',
        'category_healthcare',
        'category_school',

        // Contact Us
        'address',
        'phone',
        'fax',
        'email',

        // Social Media
        'facebook',
        'linkedin',
        'youtube',
        'instagram',
        'social_icons',
        'footer_logo'
    ];
}
