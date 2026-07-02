<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceLog extends Model
{
    protected $fillable = [
        'invoice_number', 'module', 'reference_id', 'amount',
        'tax_amount', 'currency', 'generated_by', 'file_path'
    ];
}