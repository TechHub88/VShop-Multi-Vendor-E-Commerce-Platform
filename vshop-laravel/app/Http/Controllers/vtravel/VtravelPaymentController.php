<?php

namespace App\Http\Controllers\vtravel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VtravelPaymentController extends Controller
{
        public function index()
    {
        return view('vtravel.payment.index');
    }

    /**
     * Process payment.
     */
    public function process(Request $request)
    {
        // Add your payment processing logic here
        return redirect()->route('vtravel.orders.show', ['id' => 1])
                         ->with('success', 'Payment processed successfully!');
    }
}
