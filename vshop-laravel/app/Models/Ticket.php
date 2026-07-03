<?php

// app/Models/Ticket.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model {
    use HasFactory;

    protected $fillable = [
        'ticket_name',
        'pipeline',
        'ticket_status',
        'priority',
        'category',
        'ticket_owner',
        'source',
    ];
}
