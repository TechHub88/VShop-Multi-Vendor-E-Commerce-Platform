<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminStaff extends Authenticatable
{
    use HasFactory;

     protected $table = 'admin_staffs';

 protected $fillable = [
    // Employee Details
    'employee_id',
    'first_name',
    'last_name',
    'email',
    'phone',
    'profile_photo',
    'gender',
    'dob',
    
    // Employment
    'employee_type',
    'joining_date',
    'status',  // MAKE SURE 'status' IS HERE
    'role',
    
    // Bank Details
    'account_holder_name',
    'bank_name',
    'account_number',
    'ifsc_code',
    'branch_location',  // Note: your model has 'branch_name', but controller uses 'branch_location'
    'account_type',
    'payroll_notes',
    
    // Payroll
    'basic_salary',
    'hra',
    'allowance',
    'bonus',
    'pf_percent',
    'professional_tax',
    
    // Documents
    'pan_card_file',
    'cancelled_cheque',
    'aadhaar_file',
    
    // Permissions
    'permissions',
    
    // Other
    'password',
    'created_by',
    'updated_by',
];

    protected $casts = [
        'dob' => 'date',
        'joining_date' => 'date',
        'probation_end_date' => 'date',
        'verification_date' => 'date',
        'last_salary_date' => 'date',
        'next_salary_date' => 'date',
        'last_login' => 'datetime',

        'basic_salary' => 'decimal:2',
        'hra' => 'decimal:2',
        'allowance' => 'decimal:2',
        'incentive' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deduction' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_salary' => 'decimal:2',

        'pan_verified' => 'boolean',
        'bank_verified' => 'boolean',

        'dashboard_access' => 'boolean',
        'seller_management_access' => 'boolean',
        'customer_management_access' => 'boolean',
        'order_management_access' => 'boolean',
        'finance_access' => 'boolean',
        'reports_access' => 'boolean',
        'settings_access' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo
            ? asset('storage/' . $this->profile_photo)
            : asset('assets/images/users/avatar-1.jpg');
    }

    public function getNetPayAttribute()
    {
        return (
            $this->basic_salary +
            $this->hra +
            $this->allowance +
            $this->incentive +
            $this->bonus
        ) - (
            $this->deduction +
            $this->tax_amount
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'Paid');
    }

    public function scopePendingSalary($query)
    {
        return $query->where('payment_status', 'Pending');
    }
}