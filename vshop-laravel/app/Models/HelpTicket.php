<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'manager_id',
        'full_name',
        'email',
        'phone_number',
        'ticket_title',
        'category',
        'priority',
        'issue_description',
        'order_number',
        'purchase_date',
        'payment_method',
        'attachment_path',
        'preferred_contact_method',
        'preferred_contact_time',
        'ticket_status',
        'message'
    ];

    // Relationship with products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'help_ticket_products', 'ticket_id', 'product_id')
                    ->withTimestamps();
    }

    // Relationship with manager
    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }

    // Relationship with order (if you have order_id field)
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}