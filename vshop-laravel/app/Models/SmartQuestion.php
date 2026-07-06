<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartQuestion extends Model
{
    protected $fillable = [
        'question',
        'type',
        'status'
    ];
}
