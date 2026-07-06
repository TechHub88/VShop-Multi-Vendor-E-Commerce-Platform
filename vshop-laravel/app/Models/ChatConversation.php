<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatConversation extends Model
{
    protected $fillable = [
        'type',
        'socket_id',
        'seller_id',
        'admin_id',
        'subject',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function internalSupport()
    {
        return $this->hasOne(InternalSupport::class, 'conversation_id');
    }

    public function scopeSellerAdmin($query)
    {
        return $query->where('type', 'seller_admin');
    }

    public function scopeInternal($query)
    {
        return $query->where('type', 'internal');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
