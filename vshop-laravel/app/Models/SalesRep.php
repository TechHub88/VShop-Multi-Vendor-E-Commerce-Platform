<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesRep extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email'];

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }
}
