<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class SellerStaff extends Authenticatable
{
    protected $table = 'seller_staff';

    protected $fillable = [
        'seller_id',
        'name',
        'email',
        'mobile',
        'password',
        'role',
        'can_manage_orders',
        'can_view_finance',
        'can_manage_products'
    ];

    protected $hidden = ['password'];
}