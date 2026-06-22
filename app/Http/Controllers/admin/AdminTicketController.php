<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HelpTicket;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userType = $user->type; // user table ka 'type' column
        
        \Log::info("Ticket Controller - User ID: {$user->id}, User Type: {$userType}");
        
        // Start query with user type filtering
        $query = HelpTicket::query();
        
        // Apply user type filtering
        if ($userType == 'product') {
            // Product admin should see ONLY product tickets
            $query->where('type', 'product');
            \Log::info("Product Admin - Filtering product tickets");
        } elseif ($userType == 'travel') {
            // Travel admin should see ONLY travel tickets
            $query->where('type', 'travel');
            \Log::info("Travel Admin - Filtering travel tickets");
        }
        // Admin sees all (no filter)
        
        // Apply other filters if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('ticket_status', $request->status);
        }
        
        if ($request->has('priority') && $request->priority != '') {
            $query->where('priority', $request->priority);
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_title', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Order by latest first
        $tickets = $query->orderBy('created_at', 'desc')
                        ->paginate(15);
        
        return view('admin.tickets.index', compact('tickets'));
    }
    
    public function toggleStatus(Request $request, $id)
    {
        $ticket = HelpTicket::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check if user has permission to update this ticket
        if (($userType == 'product' && $ticket->type != 'product') || 
            ($userType == 'travel' && $ticket->type != 'travel')) {
            return response()->json([
                'message' => 'You do not have permission to update this ticket.'
            ], 403);
        }
        
        $validStatuses = ['new', 'open', 'pending', 'resolved', 'closed'];
        
        if (!in_array($request->status, $validStatuses)) {
            return response()->json(['message' => 'Invalid status'], 400);
        }
        
        $ticket->ticket_status = $request->status;
        $ticket->save();
        
        return response()->json(['message' => 'Status updated successfully']);
    }
    
    public function edit($id)
    {
        $ticket = HelpTicket::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check if user has permission to edit this ticket
        if (($userType == 'product' && $ticket->type != 'product') || 
            ($userType == 'travel' && $ticket->type != 'travel')) {
            return redirect()->route('admin.tickets.index')
                ->with('error', 'You do not have permission to edit this ticket.');
        }
        
        // Get categories based on ticket type
        if ($ticket->type == 'product') {
            $categories = [
                'order_issue' => 'Order Issue',
                'payment_issue' => 'Payment Issue',
                'refund_request' => 'Refund/Return Request',
                'exchange_request' => 'Exchange Request',
                'product_inquiry' => 'Product Inquiry',
                'technical_issue' => 'Technical Issue (Website/App)',
                'delivery_issue' => 'Delivery Issue',
                'account_issue' => 'Account/Login Problem',
                'general_inquiry' => 'General Inquiry',
                'complaint' => 'Complaint'
            ];
        } else {
            $categories = [
                'booking_issue' => 'Booking Issue',
                'payment_issue' => 'Payment Issue',
                'refund_request' => 'Refund/Return Request',
                'tour_inquiry' => 'Tour Inquiry',
                'itinerary_change' => 'Itinerary Change',
                'accommodation_issue' => 'Accommodation Issue',
                'transport_issue' => 'Transport Issue',
                'guide_issue' => 'Guide Issue',
                'general_inquiry' => 'General Inquiry',
                'complaint' => 'Complaint'
            ];
        }

        $priorities = ['low', 'medium', 'high', 'urgent'];
        
        // Get orders for dropdown
        $orders = Order::orderBy('created_at', 'desc')->get();
        
        // Get selected product IDs from pivot table
        $selectedProducts = DB::table('help_ticket_products')
            ->where('ticket_id', $id)
            ->pluck('product_id')
            ->toArray();
        
        return view('admin.tickets.edit', compact(
            'ticket',
            'categories',
            'priorities',
            'orders',
            'selectedProducts',
            'userType'
        ));
    }

    public function update(Request $request, $id)
    {
        $ticket = HelpTicket::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check if user has permission to update this ticket
        if (($userType == 'product' && $ticket->type != 'product') || 
            ($userType == 'travel' && $ticket->type != 'travel')) {
            return redirect()->route('admin.tickets.index')
                ->with('error', 'You do not have permission to update this ticket.');
        }
        
        // Get categories based on ticket type for validation
        if ($ticket->type == 'product') {
            $categoryOptions = [
                'order_issue', 'payment_issue', 'refund_request', 'exchange_request',
                'product_inquiry', 'technical_issue', 'delivery_issue', 
                'account_issue', 'general_inquiry', 'complaint'
            ];
        } else {
            $categoryOptions = [
                'booking_issue', 'payment_issue', 'refund_request', 'tour_inquiry',
                'itinerary_change', 'accommodation_issue', 'transport_issue',
                'guide_issue', 'general_inquiry', 'complaint'
            ];
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'ticket_title' => 'required|string|max:255',
            'category' => 'required|in:' . implode(',', $categoryOptions),
            'priority' => 'required|in:low,medium,high,urgent',
            'issue_description' => 'required|string',
            'order_id' => 'nullable|exists:orders,id',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'purchase_date' => 'nullable|date',
            'payment_method' => 'nullable|string',
            'attachment' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'preferred_contact_method' => 'required',
            'preferred_contact_time' => 'nullable|string',
        ]);

        // Update ticket fields
        $ticket->full_name = $request->full_name;
        $ticket->email = $request->email;
        $ticket->phone_number = $request->phone_number;
        $ticket->ticket_title = $request->ticket_title;
        $ticket->category = $request->category;
        $ticket->priority = $request->priority;
        $ticket->issue_description = $request->issue_description;

        // Update order-related fields
        if($request->order_id){
            $order = Order::find($request->order_id);
            $ticket->order_number = $order->transaction_id;
            $ticket->purchase_date = $order->created_at->format('Y-m-d');
            $ticket->payment_method = $order->payment_method;
        } else {
            // If no order is selected, clear order-related fields
            $ticket->order_number = null;
            $ticket->purchase_date = null;
            $ticket->payment_method = null;
        }

        $ticket->preferred_contact_method = $request->preferred_contact_method;
        $ticket->preferred_contact_time = $request->preferred_contact_time;

        // Handle attachment update
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($ticket->attachment_path) {
                \Storage::disk('public')->delete($ticket->attachment_path);
            }
            
            $ticket->attachment_path = $request->file('attachment')
                ->store('ticket_attachments', 'public');
        }

        $ticket->save();

        // Update products in pivot table
        DB::table('help_ticket_products')->where('ticket_id', $ticket->id)->delete();
        
        if ($request->has('product_ids')) {
            foreach ($request->product_ids as $productId) {
                DB::table('help_ticket_products')->insert([
                    'ticket_id' => $ticket->id,
                    'product_id' => $productId,
                ]);
            }
        }

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket updated successfully!');
    }

    public function destroy($id)
    {
        $ticket = HelpTicket::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check if user has permission to delete this ticket
        if (($userType == 'product' && $ticket->type != 'product') || 
            ($userType == 'travel' && $ticket->type != 'travel')) {
            return back()->with('error', 'You do not have permission to delete this ticket.');
        }
        
        // Delete attachment if exists
        if ($ticket->attachment_path) {
            \Storage::disk('public')->delete($ticket->attachment_path);
        }
        
        // Delete associated products from pivot table
        DB::table('help_ticket_products')->where('ticket_id', $id)->delete();
        
        // Delete the ticket
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket deleted successfully!');
    }

    // AJAX method to get products for an order
    public function getProducts($orderId)
    {
        try {
            \Log::info("Fetching products for order ID: " . $orderId);
            
            $order = Order::with(['items.product'])->find($orderId);
            
            if (!$order) {
                \Log::warning("Order not found: " . $orderId);
                return response()->json([]);
            }
            
            $products = [];
            foreach ($order->items as $item) {
                if ($item->product) {
                    $products[] = [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'qty' => $item->quantity ?? 1,
                        'price' => $item->price
                    ];
                }
            }
            
            \Log::info("Found " . count($products) . " products");
            
            return response()->json($products);
            
        } catch (\Exception $e) {
            \Log::error("Error in getProducts: " . $e->getMessage());
            return response()->json([], 500);
        }
    }
    
    public function create()
    {
        $user = auth()->user();
        $userType = $user->type;
        
        // Get categories based on user type
        if ($userType == 'product') {
            $categories = [
                'order_issue' => 'Order Issue',
                'payment_issue' => 'Payment Issue',
                'refund_request' => 'Refund/Return Request',
                'exchange_request' => 'Exchange Request',
                'product_inquiry' => 'Product Inquiry',
                'technical_issue' => 'Technical Issue (Website/App)',
                'delivery_issue' => 'Delivery Issue',
                'account_issue' => 'Account/Login Problem',
                'general_inquiry' => 'General Inquiry',
                'complaint' => 'Complaint'
            ];
            $defaultType = 'product';
        } elseif ($userType == 'travel') {
            $categories = [
                'booking_issue' => 'Booking Issue',
                'payment_issue' => 'Payment Issue',
                'refund_request' => 'Refund/Return Request',
                'tour_inquiry' => 'Tour Inquiry',
                'itinerary_change' => 'Itinerary Change',
                'accommodation_issue' => 'Accommodation Issue',
                'transport_issue' => 'Transport Issue',
                'guide_issue' => 'Guide Issue',
                'general_inquiry' => 'General Inquiry',
                'complaint' => 'Complaint'
            ];
            $defaultType = 'travel';
        } else {
            // Admin - can choose both
            $categories = [
                'product' => [
                    'order_issue' => 'Order Issue',
                    'payment_issue' => 'Payment Issue',
                    'refund_request' => 'Refund/Return Request',
                    'exchange_request' => 'Exchange Request',
                    'product_inquiry' => 'Product Inquiry',
                    'technical_issue' => 'Technical Issue (Website/App)',
                    'delivery_issue' => 'Delivery Issue',
                    'account_issue' => 'Account/Login Problem',
                    'general_inquiry' => 'General Inquiry',
                    'complaint' => 'Complaint'
                ],
                'travel' => [
                    'booking_issue' => 'Booking Issue',
                    'payment_issue' => 'Payment Issue',
                    'refund_request' => 'Refund/Return Request',
                    'tour_inquiry' => 'Tour Inquiry',
                    'itinerary_change' => 'Itinerary Change',
                    'accommodation_issue' => 'Accommodation Issue',
                    'transport_issue' => 'Transport Issue',
                    'guide_issue' => 'Guide Issue',
                    'general_inquiry' => 'General Inquiry',
                    'complaint' => 'Complaint'
                ]
            ];
            $defaultType = null;
        }

        $priorities = ['low', 'medium', 'high', 'urgent'];

        return view('admin.tickets.create', compact('categories', 'priorities', 'userType', 'defaultType'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $userType = $user->type;
        
        // Determine ticket type based on user role
        $type = $request->type;
        if ($userType == 'product' || $userType == 'travel') {
            $type = $userType; // Force type based on user
        }
        
        // Get categories based on ticket type for validation
        if ($type == 'product') {
            $categoryOptions = [
                'order_issue', 'payment_issue', 'refund_request', 'exchange_request',
                'product_inquiry', 'technical_issue', 'delivery_issue', 
                'account_issue', 'general_inquiry', 'complaint'
            ];
        } else {
            $categoryOptions = [
                'booking_issue', 'payment_issue', 'refund_request', 'tour_inquiry',
                'itinerary_change', 'accommodation_issue', 'transport_issue',
                'guide_issue', 'general_inquiry', 'complaint'
            ];
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'ticket_title' => 'required|string|max:255',
            'category' => 'required|in:' . implode(',', $categoryOptions),
            'priority' => 'required|in:low,medium,high,urgent',
            'issue_description' => 'required|string',
            'order_id' => 'nullable|exists:orders,id',
            'attachment' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'preferred_contact_method' => 'required',
        ]);

        $ticket = new HelpTicket();

        // Set ticket type
        $ticket->type = $type;

        // Basic info
        $ticket->full_name = $request->full_name;
        $ticket->email = $request->email;
        $ticket->phone_number = $request->phone_number;
        $ticket->ticket_title = $request->ticket_title;
        $ticket->category = $request->category;
        $ticket->priority = $request->priority;
        $ticket->issue_description = $request->issue_description;

        // If admin selects order
        if($request->order_id){
            $order = Order::find($request->order_id);
            $ticket->order_number = $order->transaction_id;
            $ticket->purchase_date = $order->created_at->format('Y-m-d');
            $ticket->payment_method = $order->payment_method;
        }

        $ticket->preferred_contact_method = $request->preferred_contact_method;
        $ticket->preferred_contact_time = $request->preferred_contact_time;

        // Upload attachment
        if ($request->hasFile('attachment')) {
            $ticket->attachment_path = $request->file('attachment')
                ->store('ticket_attachments', 'public');
        }

        $ticket->save();

        $typeLabel = ucfirst($type);
        return redirect()->route('admin.tickets.index')
            ->with('success', $typeLabel . ' Ticket created successfully!');
    }

    public function searchPhone($phone)
    {
        $user = Customer::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([]);
        }

        $orders = Order::where('customer_id', $user->id)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'user' => $user,
            'orders' => $orders
        ]);
    }

    public function show(){
        // Implementation if needed
    }

    public function getTicket($id)
    {
        $ticket = HelpTicket::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check if user has permission to view this ticket
        if (($userType == 'product' && $ticket->type != 'product') || 
            ($userType == 'travel' && $ticket->type != 'travel')) {
            return response()->json([
                'html' => '<div class="alert alert-danger">You do not have permission to view this ticket.</div>'
            ]);
        }

        $html = '
            <table class="table table-bordered">
                <tr><th>ID</th><td>#'.$ticket->id.'</td></tr>
                <tr><th>Type</th><td><span class="badge bg-info">'.ucfirst($ticket->type).' Ticket</span></td></tr>
                <tr><th>Title</th><td>'.$ticket->ticket_title.'</td></tr>
                <tr><th>Customer</th><td>'.$ticket->full_name.'<br>'.$ticket->email.'</td></tr>
                <tr><th>Category</th><td>'.$ticket->category.'</td></tr>
                <tr><th>Priority</th><td>'.$ticket->priority.'</td></tr>
                <tr><th>Status</th><td>'.$ticket->ticket_status.'</td></tr>
                <tr><th>Message</th><td>'.$ticket->message.'</td></tr>
            </table>
        ';

        return response()->json(['html' => $html]);
    }

    public function reply(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:help_tickets,id',
            'reply' => 'required|string',
        ]);

        $ticket = HelpTicket::findOrFail($request->ticket_id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check if user has permission to reply to this ticket
        if (($userType == 'product' && $ticket->type != 'product') || 
            ($userType == 'travel' && $ticket->type != 'travel')) {
            return redirect()->route('admin.tickets.index')
                ->with('error', 'You do not have permission to reply to this ticket.');
        }

        // Update the ticket
        $ticket->message = $request->reply;
        $ticket->ticket_status = 'open';
        $ticket->updated_at = now();
        $ticket->save();

        // Redirect back to ticket index with success message
        return redirect()->route('admin.tickets.index')->with('success', 'Reply submitted successfully!');
    }
}