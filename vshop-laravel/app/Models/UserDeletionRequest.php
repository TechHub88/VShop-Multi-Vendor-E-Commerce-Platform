<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDeletionRequest extends Model
{
    use HasFactory;

    protected $table = 'user_deletion_requests';

    protected $fillable = [
        'user_id',
        'user_type',
        'name',
        'email',
        'status',
        'reason',
    ];
}
