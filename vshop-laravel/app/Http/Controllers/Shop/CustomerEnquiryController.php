<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\NotificationService;

class CustomerEnquiryController extends Controller
{
   public function store(Request $request)
{
    try {
        Log::info('Enquiry store called', $request->all());
        
        $validator = \Validator::make($request->all(), [
            'product_id'   => 'required|exists:products,id',
            'reason'       => 'required|string|min:3|max:500',
            'contact_type' => 'required|in:whatsapp,phone,email',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $customer = Auth::guard('customer')->user();
        Log::info('Customer check', ['authenticated' => $customer ? 'yes' : 'no', 'customer_id' => $customer->id ?? null]);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to submit enquiry.',
                'login_required' => true
            ], 401);
        }

        $product = Product::findOrFail($request->product_id);
        
        $lastEnq = Enquiry::orderBy('id', 'desc')->first();
        $newNumber = $lastEnq ? intval(substr($lastEnq->enquiry_no, 4)) + 1 : 1001;
        $enquiryNo = 'ENQ-' . $newNumber;

        $enquiry = Enquiry::create([
            'enquiry_no'      => $enquiryNo,
            'seller_id'       => $product->created_by_id,
              'product_id' => $request->product_id,   // add this line
            'user_id'         => $customer->id,
            'user_name'       => $customer->name,
            'user_email'      => $customer->email,
            'user_mobile'     => $customer->phone,
            'product_service' => $product->name,
            'message'         => $request->reason,
            'source'          => substr($request->contact_type . '_product_page', 0, 20),
            'status'          => 'New',
            'type'            => 'product',
            'source_type'     => 'vshop',
        ]);

        NotificationService::notify(
            $product->created_by_id,
            'seller',
            'enquiry',
            "New enquiry on product '{$product->name}' by {$customer->name}.",
            route('seller.enquiry.list')
        );

        NotificationService::notify(
            'all',
            'admin',
            'enquiry',
            "New enquiry on product '{$product->name}' by {$customer->name}.",
            route('admin.enquiry.index')
        );

        Log::info('Enquiry created successfully', ['enquiry_no' => $enquiryNo]);

        // Get seller's phone and email for WhatsApp/Email redirect
        $seller = \App\Models\Seller::find($product->created_by_id);
        $sellerPhone = null;
        $sellerEmail = null;
        if ($seller) {
            $sellerEmail = $seller->email;
            
            // Prefer mobile, fallback to phone
            $rawPhone = $seller->mobile ?: $seller->phone;
            if ($rawPhone) {
                // Clean phone: remove spaces, dashes, +, country code prefix
                $sellerPhone = preg_replace('/[^0-9]/', '', $rawPhone);
                // If 10 digits, prepend India code 91
                if (strlen($sellerPhone) === 10) {
                    $sellerPhone = '91' . $sellerPhone;
                }
            }
            
            // Send email to seller using Laravel Mail
            if ($sellerEmail && $request->contact_type == 'email') {
                try {
                    \Illuminate\Support\Facades\Mail::to($sellerEmail)->send(new \App\Mail\EnquiryMail($enquiry));
                    Log::info('Enquiry email sent to seller', ['seller_email' => $sellerEmail]);
                } catch (\Exception $e) {
                    Log::error('Failed to send enquiry email: ' . $e->getMessage());
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Enquiry submitted. Seller will contact you soon.',
            'seller_phone' => $sellerPhone,
            'seller_email' => $sellerEmail,
            'product_name' => $product->name,
            'enquiry_no' => $enquiryNo,
        ]);
        
    } catch (\Exception $e) {
        Log::error('Enquiry store exception: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()  // Show actual error for debugging
        ], 500);
    }
}
}
