<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\About;
use App\Models\Category;
use App\Models\Footer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CartItem;

class AddressController extends Controller
{
    
    
    // Show checkout page with addresses
    public function index(Request $request)
    {
        $customerId = Auth::guard('customer')->id();

        // About, categories, footer
        $about = About::first(); 
        $categories = Category::all(); 
        $footer = Footer::first();

        // Cart Items with product details
        $cartItems = CartItem::where('customer_id', $customerId)
            ->with('product.branch') 
            ->get();

        // Calculate totals the same way as cart page
        $grandTotal = 0;
        $totalUniqueItems = 0;
        $totalQuantity = 0;
        $totalSubtotal = 0;
        $totalGST = 0;
        $totalEmbroideryPrice = 0;
        
        foreach($cartItems as $item) {
            if($item->combo_id) {
                // Combo product: use new columns
                $mainProductTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
                $comboProductTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
                $itemSubtotal = $mainProductTotal + $comboProductTotal;

                $itemEmbroideryTotal = $item->embroidery_total_price ?? 0;
                $totalEmbroideryPrice += $itemEmbroideryTotal;

                $taxableAmount = $itemSubtotal + $itemEmbroideryTotal;
                $gstPercent = $item->gst_percent ?? 5; // Default to 5% if not set
                $itemGST = $taxableAmount * ($gstPercent / 100);
                $itemTotal = $taxableAmount + $itemGST;
                
                $grandTotal += $itemTotal;
                $totalSubtotal += $itemSubtotal;
                $totalGST += $itemGST;
                $totalUniqueItems++;
                $totalQuantity += ($item->main_product_quantity ?? 1) + ($item->combo_product_quantity ?? 1);
            } else {
                // Individual product: use main_product_price and main_product_quantity
                $itemPrice = $item->main_product_price ?? 0;
                $itemQuantity = $item->main_product_quantity ?? 1;
                $itemSubtotal = $itemPrice * $itemQuantity;

                $itemEmbroideryTotal = $item->embroidery_total_price ?? 0;
                $totalEmbroideryPrice += $itemEmbroideryTotal;

                $taxableAmount = $itemSubtotal + $itemEmbroideryTotal;
                $gstPercent = $item->gst_percent ?? 5; // Default to 5% if not set
                $itemGST = $taxableAmount * ($gstPercent / 100);
                $itemTotal = $taxableAmount + $itemGST;
                
                $grandTotal += $itemTotal;
                $totalSubtotal += $itemSubtotal;
                $totalGST += $itemGST;
                $totalUniqueItems++;
                $totalQuantity += $itemQuantity;
            }
        }

        // Addresses for logged-in customer
        $addresses = Address::where('customer_id', $customerId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get selected address ID from query parameter (when redirected from add address)
        $selectedAddressId = $request->query('selected_address');

        // Expected delivery (7 days from now)
        $expectedDelivery = now()->addDays(7)->format('d M Y (l)');

        // Get GST percent from cart items (use first item's GST or default to 5%)
        $gstPercent = $cartItems->first()->gst_percent ?? 5;
        $appliedCoupon = $cartItems->firstWhere('coupon_code', '!=', null);

$totalDiscount = $cartItems->sum('discount_amount'); // Total coupon discount

$youSave = $totalDiscount; // If you want, add product offer savings also

        return view('frontend.address', compact(
            'cartItems',
            'grandTotal',
            'totalSubtotal',
            'totalGST',
            'totalEmbroideryPrice',
            'totalUniqueItems',
            'totalQuantity',
            'gstPercent',
            'about',
            'categories',
            'footer',
            'addresses',
            'expectedDelivery',
              'totalDiscount',
    'youSave',
    'appliedCoupon',
    'selectedAddressId'
        ));
    }

    // Show add address page
    public function create()
    {
        $customerId = Auth::guard('customer')->id();
        
        // Get existing addresses for this customer
        $existingAddresses = Address::where('customer_id', $customerId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $about = About::first(); 
        $categories = Category::all(); 
        $footer = Footer::first();
           // Addresses for logged-in customer
        $addresses = Address::where('customer_id', $customerId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('frontend.addaddress', compact(
            'about', 
            'categories', 
            'footer', 'addresses'
        ));
    }

    // Store new address
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'company_name' => 'nullable|string|max:255',
            'gstin' => 'nullable|string|max:50',
            'phone' => 'required|string|max:15',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address_line1' => 'required|string|max:500',
            'pincode' => 'required|string|max:10',
            'address_type' => 'required|in:shipping,billing,both',
        ]);

        $customerId = Auth::guard('customer')->id();
        
        // If this address is marked as default, remove default from other addresses
        if ($request->has('is_default') && $request->is_default == 1) {
            Address::where('customer_id', $customerId)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $address = Address::create([
            'customer_id' => $customerId,
            'name' => $request->name,
            'email' => $request->email,
            'company_name' => $request->company_name,
            'gstin' => $request->gstin,
            'phone' => $request->phone,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'address_line1' => $request->address_line1,
            'pincode' => $request->pincode,
            'address_type' => $request->address_type,
            'is_default' => $request->has('is_default') ? $request->is_default : false,
        ]);

        // Redirect to checkout page with the newly added address selected
        return redirect()->route('frontend.address.index', ['selected_address' => $address->id])
            ->with('success', 'Address added successfully ✅');
    }

    // Update existing address (new method for AJAX update)
    public function update(Request $request, $id)
    {
        $customerId = Auth::guard('customer')->id();
        
        // Verify the address belongs to the logged-in customer
        $address = Address::where('id', $id)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'company_name' => 'nullable|string|max:255',
            'gstin' => 'nullable|string|max:50',
            'phone' => 'required|string|max:15',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address_line1' => 'required|string|max:500',
            'pincode' => 'required|string|max:10',
            'address_type' => 'required|in:shipping,billing,both',
        ]);

        // If this address is marked as default, remove default from other addresses
        if ($request->has('is_default') && $request->is_default == 1) {
            Address::where('customer_id', $customerId)
                ->where('id', '!=', $id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $address->update([
            'name' => $request->name,
            'email' => $request->email,
            'company_name' => $request->company_name,
            'gstin' => $request->gstin,
            'phone' => $request->phone,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'address_line1' => $request->address_line1,
            'pincode' => $request->pincode,
            'address_type' => $request->address_type,
            'is_default' => $request->has('is_default') ? $request->is_default : $address->is_default,
        ]);

        return redirect()->route('add-address')
            ->with('success', 'Address updated successfully ✅');
    }

    // Delete an address
    public function destroy($id)
    {
        $customerId = Auth::guard('customer')->id();

        // Find the address belonging to logged-in customer
        $address = Address::where('id', $id)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        // If deleting default address, set another address as default
        if ($address->is_default) {
            $newDefault = Address::where('customer_id', $customerId)
                ->where('id', '!=', $id)
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $address->delete();

        return redirect()->back()->with('success', 'Address deleted successfully ✅');
    }

    // Set address as default (new method)
    public function setDefault($id)
    {
        $customerId = Auth::guard('customer')->id();
        
        // Verify the address belongs to the logged-in customer
        $address = Address::where('id', $id)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        // Remove default from all other addresses
        Address::where('customer_id', $customerId)
            ->where('id', '!=', $id)
            ->update(['is_default' => false]);

        // Set this address as default
        $address->update(['is_default' => true]);

        return redirect()->back()
            ->with('success', 'Default address updated successfully ✅');
    }
    
    // Save shipping charge to cart items
    public function saveShippingCharge(Request $request)
    {
        try {
            $request->validate([
                'shipping_charge' => 'required|numeric|min:0', // Allow 0 for free shipping
                'address_id' => 'required|exists:addresses,id',
            ]);

            $customerId = Auth::guard('customer')->id();
            
            // Verify the address belongs to the logged-in customer
            $address = Address::where('id', $request->address_id)
                ->where('customer_id', $customerId)
                ->firstOrFail();

            // Get all cart items for this customer
            $cartItems = CartItem::where('customer_id', $customerId)->get();
            
            if ($cartItems->isEmpty()) {
                Log::error('Save shipping charge: No items in cart for customer ' . $customerId);
                return response()->json([
                    'status' => 'error',
                    'message' => 'No items in cart'
                ], 400);
            }

            // Calculate shipping charge per item (distribute evenly)
            $shippingCharge = (float) $request->shipping_charge;
            $shippingChargePerItem = $shippingCharge / $cartItems->count();

            Log::info('Saving shipping charge', [
                'customer_id' => $customerId,
                'total_shipping_charge' => $shippingCharge,
                'shipping_charge_per_item' => $shippingChargePerItem,
                'cart_items_count' => $cartItems->count()
            ]);

            // Update each cart item with shipping charge
            $updatedCount = 0;
            foreach ($cartItems as $item) {
                $item->courier_charge = $shippingChargePerItem;
                $item->save();
                $updatedCount++;
            }

            // Refresh cart items to ensure data is updated
            $cartItems->each(function($item) {
                $item->refresh();
            });

            Log::info('Shipping charge saved successfully', [
                'customer_id' => $customerId,
                'total_shipping_charge' => $shippingCharge,
                'shipping_charge_per_item' => $shippingChargePerItem,
                'updated_items' => $updatedCount
            ]);

            // Store selected address ID and shipping charge flag in session for use in payment method
            session([
                'selected_address_id' => $request->address_id,
                'shipping_charge_applied' => true,
                'shipping_charge_amount' => $shippingCharge
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Shipping charge saved successfully',
                'shipping_charge' => $shippingCharge,
                'items_updated' => $updatedCount,
                'shipping_charge_per_item' => $shippingChargePerItem
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving shipping charge: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error saving shipping charge: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Calculate shipping charge via Shiprocket API
    public function calculateShippingCharge(Request $request)
    {
        $request->validate([
            'delivery_postcode' => 'required|string|max:10',
        ]);

        $customerId = Auth::guard('customer')->id();

        // Get cart items to calculate declared value
        $cartItems = CartItem::where('customer_id', $customerId)->get();

        // Calculate declared value (subtotal without GST and courier charge)
        $declaredValue = 0;
        foreach ($cartItems as $item) {
            if ($item->combo_id) {
                $mainProductTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
                $comboProductTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
                $declaredValue += $mainProductTotal + $comboProductTotal;
            } else {
                $itemPrice = $item->main_product_price ?? 0;
                $itemQuantity = $item->main_product_quantity ?? 1;
                $declaredValue += $itemPrice * $itemQuantity;
            }
        }

        // Default shipment weight
        $weight = "1";

        // Fixed pickup postcode
        $pickupPostcode = "380009";
        $deliveryPostcode = $request->delivery_postcode;

        try {
            // Step 1: Get Shiprocket token
            $loginResponse = $this->getShiprocketToken();

            if (!isset($loginResponse['token'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to authenticate with Shiprocket'
                ], 500);
            }

            $token = $loginResponse['token'];

            // Step 2: Fetch shipping charges
            $shippingResponse = $this->getShippingCharges(
                $token,
                $pickupPostcode,
                $deliveryPostcode,
                $weight,
                $declaredValue
            );
            if (!is_array(value: $shippingResponse)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid shipping response'
                ], 500);
            }

            Log::info('Shiprocket full response', $shippingResponse);

            // Step 3: Extract couriers correctly
            if (
                !isset($shippingResponse['available_courier_companies']) ||
                !is_array($shippingResponse['available_courier_companies'])
            ) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No courier companies available'
                ], 404);
            }

            $couriers = $shippingResponse['available_courier_companies'];
            // Step 4: Find lowest freight charge
            $lowestFreightCharge = null;
            $lowestCourier = null;
            
            foreach ($couriers as $courier) {

                if (!is_array($courier)) {
                    continue;
                }

                if (!isset($courier['freight_charge']) || $courier['freight_charge'] === '') {
                    continue;
                }

                $freightCharge = (float) $courier['freight_charge'];

                // Ignore invalid charges
                if ($freightCharge <= 0) {
                    continue;
                }

                if ($lowestFreightCharge === null || $freightCharge < $lowestFreightCharge) {
                    $lowestFreightCharge = $freightCharge;
                    $lowestCourier = $courier;
                }
            }

            // Safety check
            if ($lowestFreightCharge === null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No valid shipping option found'
                ], 404);
            }

            Log::info('Lowest freight courier selected', $lowestCourier);

            // Step 5: Return lowest shipping charge
            return response()->json([
                'status' => 'success',
                'shipping_charge' => round($lowestFreightCharge, 2)
            ]);

        } catch (\Exception $e) {
            Log::error('Shipping calculation error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error calculating shipping charge'
            ], 500);
        }
    }

    
    // Get Shiprocket authentication token
    private function getShiprocketToken()
    {
        try {
            $response = Http::post('https://apiv2.shiprocket.in/v1/external/auth/login', [
                'email' => 'souvik2.webmantra@gmail.com',
                'password' => 'U05yPJqLpT*NCE32^frMOzfxXIX582bs'
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            Log::error('Shiprocket login error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Shiprocket login error: ' . $e->getMessage());
            return null;
        }
    }
    
    // Get shipping charges from Shiprocket
    private function getShippingCharges($token, $pickupPostcode, $deliveryPostcode, $weight, $declaredValue)
    {
        try {
            // Prepare request data as JSON body (matching curl --data structure)
            $requestData = json_encode([
                'pickup_postcode' => $pickupPostcode,
                'delivery_postcode' => $deliveryPostcode,
                'weight' => $weight,
                'cod' => 0,
                'declared_value' => $declaredValue
            ]);
            
            // Initialize curl
            $curl = curl_init();
            
            // Set curl options matching the exact curl structure
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/courier/serviceability/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => $requestData,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ],
            ]);
            
            // Execute curl request
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            
            // Close curl
            curl_close($curl);
            
            // Check for curl errors
            if ($curlError) {
                Log::error('Shiprocket serviceability curl error: ' . $curlError);
                return null;
            }
            
            // Check HTTP status code
            if ($httpCode >= 200 && $httpCode < 300) {
                $data = json_decode($response, true);
                
                if ($data === null) {
                    Log::error('Shiprocket serviceability error: Invalid JSON response');
                    return null;
                }
                
                // The API might return data in different formats, handle both
                if (isset($data['data'])) {
                    // If data key exists, return its value
                    return is_array($data['data']) ? $data['data'] : [$data['data']];
                } elseif (is_array($data)) {
                    // Check if it's an array of couriers or a single object
                    if (isset($data['freight_charge'])) {
                        // Single courier object
                        return $data;
                    } else {
                        // Array of couriers
                        return $data;
                    }
                }
            } else {
                Log::error('Shiprocket serviceability error: HTTP ' . $httpCode . ' - ' . $response);
                return null;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Shiprocket serviceability error: ' . $e->getMessage());
            return null;
        }
    }
    
    
}
