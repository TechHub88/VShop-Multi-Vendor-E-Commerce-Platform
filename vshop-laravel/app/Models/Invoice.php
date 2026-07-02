<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['prefix','style','company_name','company_address','company_email','company_phone','company_gst','footer','format'];
    protected $casts = [
        'footer' => 'array',
        'format' => 'array',
    ];
}

?>
