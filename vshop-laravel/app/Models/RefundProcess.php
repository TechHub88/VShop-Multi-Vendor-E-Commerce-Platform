<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundProcess extends Model
{
    use HasFactory;

    protected $table = 'refund_processes';

    protected $fillable = [
        'step_name',
        'description',
        'is_active',
    ];
}
