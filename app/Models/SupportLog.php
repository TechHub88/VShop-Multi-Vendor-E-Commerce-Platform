<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportLog extends Model
{
    protected $table = 'support_logs';

    protected $fillable = [
        'transaction_id',
        'message'
    ];

    // ✅ Relationship: belongs to transaction
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}