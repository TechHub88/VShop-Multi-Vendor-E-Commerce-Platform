<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Coupon;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->get('search', '');
        
        if (strlen($searchTerm) < 2) {
            return response()->json([]);
        }
        
        $results = [
            'products' => $this->searchProducts($searchTerm),
            'orders' => $this->searchOrders($searchTerm),
            'sellers' => $this->searchSellers($searchTerm),
            'customers' => $this->searchCustomers($searchTerm),
            'tickets' => $this->searchTickets($searchTerm),
            'coupons' => $this->searchCoupons($searchTerm),
        ];
        
        return response()->json($results);
    }
    
    private function searchProducts($searchTerm)
    {
        return Product::where('name', 'like', "%{$searchTerm}%")
            ->orWhere('sku', 'like', "%{$searchTerm}%")
            ->orWhere('description', 'like', "%{$searchTerm}%")
            ->select('id', 'name', 'sku')
            ->limit(10)
            ->get();
    }
    
    private function searchOrders($searchTerm)
    {
        return Order::where('order_number', 'like', "%{$searchTerm}%")
            ->orWhere('id', 'like', "%{$searchTerm}%")
            ->select('id', 'order_number')
            ->limit(10)
            ->get();
    }
    
    private function searchSellers($searchTerm)
    {
        return User::where('role', 'seller')
            ->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('shop_name', 'like', "%{$searchTerm}%");
            })
            ->select('id', 'name', 'email', 'shop_name')
            ->limit(10)
            ->get()
            ->map(function($seller) {
                return [
                    'id' => $seller->id,
                    'name' => $seller->shop_name ?? $seller->name,
                    'email' => $seller->email
                ];
            });
    }
    
    private function searchCustomers($searchTerm)
    {
        return User::where('role', 'customer')
            ->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('phone', 'like', "%{$searchTerm}%");
            })
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();
    }
    
    private function searchTickets($searchTerm)
    {
        return Ticket::where('subject', 'like', "%{$searchTerm}%")
            ->orWhere('ticket_id', 'like', "%{$searchTerm}%")
            ->orWhere('message', 'like', "%{$searchTerm}%")
            ->select('id', 'subject', 'ticket_id')
            ->limit(10)
            ->get();
    }
    
    private function searchCoupons($searchTerm)
    {
        return Coupon::where('code', 'like', "%{$searchTerm}%")
            ->orWhere('description', 'like', "%{$searchTerm}%")
            ->select('id', 'code')
            ->limit(10)
            ->get();
    }
}