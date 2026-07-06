<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerInvoice extends Model
{
    protected $table = 'seller_invoices';

    protected $fillable = [
        'seller_id',
        'invoice_no',
        'total_amount',
        'taxable_amount',
        'gst_amount',
        'net_amount',
        'invoice_date'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
