<?php
// app/Models/RejectedBanner.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RejectedBanner extends Model
{
    protected $fillable = ['seller_id', 'file_hash', 'reason'];
}