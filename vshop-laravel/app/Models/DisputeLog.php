<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisputeLog extends Model
{
    protected $table = 'dispute_logs';

    protected $fillable = [
        'transaction_id',
        'user_id',
        'message',
        'status'
    ];

    // ✅ Relationship: belongs to transaction
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // ✅ Relationship: belongs to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
