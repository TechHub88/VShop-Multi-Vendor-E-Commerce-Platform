<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
     protected $fillable = ['name','type','created_by','created_by_id'];

    // One-to-many relationship
    public function terms()
    {
        return $this->hasMany(AttributeTerm::class, 'attribute_id');
    }

}
