<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscalationRule extends Model
{
    protected $fillable = ['rule_name', 'condition', 'action', 'assign_to', 'priority', 'is_active'];
}
