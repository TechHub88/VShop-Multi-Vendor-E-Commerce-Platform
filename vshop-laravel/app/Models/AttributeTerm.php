<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeTerm extends Model
{
  

    protected $fillable = ['attribute_id', 'term','colour_code'];
}
