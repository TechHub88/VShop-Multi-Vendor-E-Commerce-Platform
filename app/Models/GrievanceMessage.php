<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrievanceMessage extends Model
{
    protected $table = 'grievance_messages';

    protected $fillable = [
        'grievance_id',
        'sender',
        'message'
    ];

    public $timestamps = true;

    /* ================= RELATIONS ================= */

    public function grievance()
    {
        return $this->belongsTo(Grievance::class, 'grievance_id');
    }

    /* ================= HELPERS ================= */

    public function isSeller()
    {
        return $this->sender === 'seller';
    }

    public function isAdmin()
    {
        return $this->sender === 'admin';
    }

    public function isBuyer()
    {
        return $this->sender === 'buyer';
    }

    public function senderBadge()
    {
        return match($this->sender) {
            'seller' => 'primary',
            'admin' => 'success',
            'buyer' => 'info',
            default => 'secondary'
        };
    }
}