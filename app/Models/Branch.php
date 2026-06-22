<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends Model
{
    protected $table = 'branches';

    protected $fillable = [
        'brand_id',
        'user_id',
        'address',
        'city',
        'state',
        'pincode',
    ];

    /**
     * Get the brand (institution detail) for this branch.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(InstitutionDetail::class, 'brand_id');
    }

    /**
     * Get the user who owns/manages this branch.
     */

    
    public function manager()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');  // manager table
}

}
