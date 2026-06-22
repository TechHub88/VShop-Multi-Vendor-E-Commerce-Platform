<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    // Table associated with the model
    protected $table = 'notification_settings';

    // Fillable fields for mass assignment
    protected $fillable = [
        'key', // The unique identifier for the notification (e.g., 'order_confirmed')
        'message', // The message or description of the notification
        'notify_email', // Boolean to enable/disable email notification
        'notify_sms', // Boolean to enable/disable SMS notification
        'notify_web', // Boolean to enable/disable web notification
    ];

    // The attributes that should be cast to native types
    protected $casts = [
        'notify_email' => 'boolean',
        'notify_sms' => 'boolean',
        'notify_web' => 'boolean',
    ];
}
