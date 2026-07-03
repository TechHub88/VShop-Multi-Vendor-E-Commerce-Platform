<?php
// app/Models/CustomerLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLog extends Model
{
    protected $fillable = [
        'customer_id',
        'log_date',
        'channel',
        'type',
        'notes',
        'handled_by'
    ];
}