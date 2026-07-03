<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grievance;
use App\Models\GrievanceMessage;
use App\Models\Seller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\EscalationRule;
use App\Models\AdminStaff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class GrievanceController extends Controller
{
    /**
     * Display the grievance dashboard + ticket list.
     */
    public function index(Request $request)
    {
        // 1. Auto-seed if empty
        $this->checkAndSeedGrievances();

        // 2. Auto-escalation sweep
        $this->processAutoEscalation();

        // 3. Query builder
        $query = Grievance::with(['seller', 'buyer', 'messages']);

        // Admin only sees escalated/assigned or platform-only (non buyer-only) tickets
        $query->where(function ($q) {
            $q->whereNull('buyer_id')
              ->orWhere('status', 'escalated')
              ->orWhereNotNull('assigned_to');
        });

        // Filters
        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('priority'))  $query->where('priority', $request->priority);
        if ($request->filled('type'))      $query->where('type', $request->type);
        if ($request->filled('seller_id')) $query->where('seller_id', $request->seller_id);

        // Date range filter (11.2 screenshot feature)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $grievances = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Form dropdowns
        $sellers     = Seller::select('id', 'name')->get();
        $buyers      = Customer::select('id', 'name', 'email')->get();
        $orders      = Order::select('id', 'transaction_id', 'total_amount')->get();
        $products    = Product::select('id', 'name')->get();
        $staffMembers = AdminStaff::get();

        return view('admin.grievance.index', compact(
            'grievances', 'sellers', 'buyers', 'orders', 'products', 'staffMembers'
        ));
    }

    /**
     * Store a newly created grievance.
     * Handles file attachments (stored as JSON in resolution_note prefix or a dedicated column).
     */
    public function store(Request $request)
    {
        $request->validate([
            'type'              => 'required|in:seller_buyer,seller_platform,lead_dispute,fake_enquiry,click_fraud',
            'subject'           => 'required|string|max:255',
            'message'           => 'required|string',
            'priority'          => 'required|in:low,medium,high',
            'seller_id'         => 'nullable',
            'buyer_id'          => 'nullable',
            'order_id'          => 'nullable',
            'amount'            => 'nullable|numeric',
            'no_response_hours' => 'required|integer|min:1|max:168',
            'attachments'       => 'nullable|array',
            'attachments.*'     => 'nullable|file|max:5120',
        ]);

        // Handle file uploads
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('grievances/attachments', 'public');
                $attachmentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => round($file->getSize() / 1024) . ' KB',
                    'type' => $file->getMimeType(),
                ];
            }
        }

        $grievance = new Grievance();
        $grievance->type       = $request->type;
        $grievance->subject    = $request->subject;
        $grievance->message    = $request->message;
        $grievance->priority   = $request->priority;
        $grievance->seller_id  = $request->seller_id ?: null;
        $grievance->buyer_id   = $request->buyer_id ?: null;
        $grievance->order_id   = $request->order_id ?: null;
        $grievance->amount     = $request->amount;
        $grievance->status     = 'open';
        $grievance->assigned_to = null;
        $grievance->resolution_note = "Escalation Rule: No response in " . $request->no_response_hours . " hours.";
        $grievance->save();

        // Initial complaint message
        GrievanceMessage::create([
            'grievance_id' => $grievance->id,
            'sender'       => 'admin',
            'message'      => 'Ticket opened: ' . $request->message,
        ]);

        // Attachment log message
        if (!empty($attachmentPaths)) {
            $fileNames = implode(', ', array_column($attachmentPaths, 'name'));
            GrievanceMessage::create([
                'grievance_id' => $grievance->id,
                'sender'       => 'admin',
                'message'      => '[Attachments uploaded: ' . $fileNames . ']',
            ]);
        }

        // Auto-escalate for high-value orders
        if ($request->filled('order_id')) {
            $order = Order::find($request->order_id);
            $orderValueLimit = (float) \App\Models\Setting::getByKey('grievance_escalation_order_value', 5000);
            $seniorAgent     = \App\Models\Setting::getByKey('grievance_escalation_agent', 'Senior Admin');
            if ($order && $order->total_amount > $orderValueLimit) {
                $grievance->status       = 'escalated';
                $grievance->assigned_to  = $seniorAgent;
                $grievance->resolution_note = 'Auto-escalated: High-value order issue (> ' . $orderValueLimit . ')';
                $grievance->save();
            }
        }

        return redirect()->route('admin.grievance.index')
            ->with('success', 'Complaint filed and ticket created successfully.');
    }

    /**
     * AJAX: Show grievance details for the modal (11.3 Ticket Details Page).
     */
    public function show($id)
    {
        $grievance = Grievance::with(['seller', 'buyer', 'messages'])->findOrFail($id);

        $grievance->formatted_date    = $grievance->created_at->format('d M Y h:i A');
        $grievance->formatted_updated = $grievance->updated_at->format('d M Y h:i A');

        $order = $grievance->order_id ? Order::find($grievance->order_id) : null;

        return response()->json([
            'success'   => true,
            'grievance' => $grievance,
            'order'     => $order,
        ]);
    }

    /**
     * Assign grievance to a team member.
     */
    public function assign(Request $request, $id)
    {
        $request->validate(['assigned_to' => 'required|string|max:255']);

        $grievance = Grievance::findOrFail($id);
        $grievance->assigned_to = $request->assigned_to;
        if ($grievance->status === 'open') {
            $grievance->status = 'in_progress';
        }
        $grievance->save();

        GrievanceMessage::create([
            'grievance_id' => $id,
            'sender'       => 'admin',
            'message'      => "Ticket assigned to agent: " . $request->assigned_to,
        ]);

        return response()->json(['success' => true, 'message' => 'Ticket assigned successfully.']);
    }

    /**
     * Resolve and close grievance.
     */
    public function resolve(Request $request, $id)
    {
        $request->validate(['resolution_note' => 'required|string']);

        $grievance = Grievance::findOrFail($id);
        $grievance->status          = 'resolved';
        $grievance->resolution_note = $request->resolution_note;
        $grievance->save();

        GrievanceMessage::create([
            'grievance_id' => $id,
            'sender'       => 'admin',
            'message'      => "Ticket Resolved. Resolution: " . $request->resolution_note,
        ]);

        return response()->json(['success' => true, 'message' => 'Ticket resolved successfully.']);
    }

    /**
     * Manually escalate the grievance.
     */
    public function escalate(Request $request, $id)
    {
        $request->validate([
            'escalation_reason' => 'required|string',
            'escalated_to'      => 'required|string',
        ]);

        $grievance = Grievance::findOrFail($id);
        $grievance->status          = 'escalated';
        $grievance->assigned_to     = $request->escalated_to;
        $grievance->resolution_note = "Escalated: " . $request->escalation_reason;

        // Use model helper if columns exist
        if (in_array('escalated_to', $grievance->getFillable())) {
            $grievance->escalated_to = $request->escalated_to;
            $grievance->escalated_at = now();
        }
        $grievance->save();

        GrievanceMessage::create([
            'grievance_id' => $id,
            'sender'       => 'admin',
            'message'      => "Ticket manually escalated to [{$request->escalated_to}]. Reason: " . $request->escalation_reason,
        ]);

        return response()->json(['success' => true, 'message' => 'Ticket escalated successfully.']);
    }

    /**
     * Reply to the grievance ticket message board.
     */
    public function reply(Request $request)
    {
        $request->validate([
            'grievance_id' => 'required|exists:grievances,id',
            'message'      => 'required|string',
            'sender'       => 'required|in:admin,seller,buyer',
        ]);

        GrievanceMessage::create([
            'grievance_id' => $request->grievance_id,
            'sender'       => $request->sender,
            'message'      => $request->message,
        ]);

        $grievance = Grievance::findOrFail($request->grievance_id);
        $grievance->touch();

        if ($request->sender === 'admin') {
            if ($grievance->buyer_id) {
                \App\Services\NotificationService::notify(
                    $grievance->buyer_id,
                    'customer',
                    'grievance',
                    "Admin replied to your grievance ticket #{$grievance->id}.",
                    route('vshop.myaccount')
                );
            }
            if ($grievance->seller_id) {
                \App\Services\NotificationService::notify(
                    $grievance->seller_id,
                    'seller',
                    'grievance',
                    "Admin replied to grievance ticket #{$grievance->id}.",
                    route('seller.grievance.list')
                );
            }
        } else {
            // Seller or Buyer replied -> notify admin
            $senderName = $request->sender === 'seller' ? ($grievance->seller->name ?? 'Seller') : ($grievance->buyer->name ?? 'Buyer');
            \App\Services\NotificationService::notify(
                'all',
                'admin',
                'grievance',
                "{$senderName} replied to grievance ticket #{$grievance->id}.",
                route('admin.grievance.show', $grievance->id)
            );
        }

        return response()->json(['success' => true, 'message' => 'Reply posted successfully.']);
    }

    // ===================================================================
    // PRIVATE: Auto-Escalation Logic
    // ===================================================================
    private function processAutoEscalation()
    {
        $grievances = Grievance::whereNotIn('status', ['resolved', 'closed', 'escalated'])->get();

        $orderValueLimit = (float) \App\Models\Setting::getByKey('grievance_escalation_order_value', 5000);
        $hoursLimitCfg   = (int)   \App\Models\Setting::getByKey('grievance_escalation_hours', 24);
        $seniorAgent     = \App\Models\Setting::getByKey('grievance_escalation_agent', 'Senior Admin');

        foreach ($grievances as $grv) {
            $shouldEscalate   = false;
            $escalationReason = '';

            // Rule 1: High-value order
            if ($grv->order_id) {
                $order = Order::find($grv->order_id);
                if ($order && $order->total_amount > $orderValueLimit) {
                    $shouldEscalate   = true;
                    $escalationReason = "Auto-Escalation: High-Value Order Issue (Value > " . number_format($orderValueLimit) . ")";
                }
            }

            // Rule 2: No-response timeout
            if (!$shouldEscalate) {
                $hoursLimit = $hoursLimitCfg;
                if (preg_match('/No response in (\d+) hours/i', $grv->resolution_note ?? '', $matches)) {
                    $hoursLimit = intval($matches[1]);
                }
                if (Carbon::now()->diffInHours($grv->updated_at) >= $hoursLimit) {
                    $shouldEscalate   = true;
                    $escalationReason = "Auto-Escalation: No Response in {$hoursLimit} hours";
                }
            }

            if ($shouldEscalate) {
                $grv->status          = 'escalated';
                $grv->assigned_to     = $seniorAgent;
                $grv->resolution_note = $escalationReason;
                if (in_array('escalated_to', $grv->getFillable())) {
                    $grv->escalated_to = $seniorAgent;
                    $grv->escalated_at = now();
                }
                $grv->save();

                GrievanceMessage::create([
                    'grievance_id' => $grv->id,
                    'sender'       => 'admin',
                    'message'      => "[System Event] {$escalationReason}. Escalated to {$seniorAgent}.",
                ]);
            }
        }
    }

    // ===================================================================
    // PRIVATE: Seed Demo Data
    // ===================================================================
    private function checkAndSeedGrievances()
    {
        if (Grievance::count() > 0) return;

        $sellerId  = Seller::first()?->id;
        $buyerId   = Customer::first()?->id;
        $orderId   = Order::first()?->id;
        $productId = Product::first()?->id;

        $samples = [
            [
                'type' => 'seller_buyer', 'subject' => 'Defective item received - Order #10024',
                'message'  => 'Buyer claims the item received is broken. Seller claims it was shipped in perfect condition.',
                'priority' => 'high',   'status' => 'open',
                'seller_id' => $sellerId, 'buyer_id' => $buyerId, 'order_id' => $orderId,
                'amount' => 1250.00,    'resolution_note' => 'Escalation Rule: No response in 12 hours.',
            ],
            [
                'type' => 'seller_platform', 'subject' => 'Delayed Settlement Payout (May Cycle)',
                'message'  => 'Seller states the weekly payout has not reached their bank. Finance shows payout successful.',
                'priority' => 'medium', 'status' => 'in_progress',
                'seller_id' => $sellerId, 'buyer_id' => null, 'order_id' => null,
                'amount' => 8400.00, 'assigned_to' => 'Finance Team Lead',
                'resolution_note' => 'Escalation Rule: No response in 24 hours.',
            ],
            [
                'type' => 'lead_dispute', 'subject' => 'Invalid Contact Number on Premium Lead #L-904',
                'message'  => 'Seller paid 100 credits for this lead but the buyer phone is out of service.',
                'priority' => 'low', 'status' => 'resolved',
                'seller_id' => $sellerId, 'buyer_id' => null, 'order_id' => null,
                'amount' => 100.00, 'resolution_note' => 'Refunded 100 lead credits to seller wallet.',
            ],
            [
                'type' => 'fake_enquiry', 'subject' => 'Bulk spam enquiries from suspicious account',
                'message'  => 'Multiple automated queries received on products within 5 minutes. Looks like scraper activity.',
                'priority' => 'medium', 'status' => 'open',
                'seller_id' => $sellerId, 'buyer_id' => $buyerId, 'order_id' => null,
                'amount' => null, 'resolution_note' => 'Escalation Rule: No response in 48 hours.',
            ],
            [
                'type' => 'click_fraud', 'subject' => 'Banner click spike investigation request',
                'message'  => 'Banner #AD-771 received 500 clicks in 1 hour with zero conversion. Suspecting click fraud.',
                'priority' => 'high', 'status' => 'escalated',
                'seller_id' => $sellerId, 'buyer_id' => null, 'order_id' => null,
                'amount' => 5400.00, 'assigned_to' => 'Senior Admin',
                'resolution_note' => 'Auto-Escalation: Click Fraud threshold exceeded',
            ],
        ];

        foreach ($samples as $sample) {
            $grv = Grievance::create($sample);

            GrievanceMessage::create([
                'grievance_id' => $grv->id, 'sender' => 'seller',
                'message' => $grv->message, 'created_at' => Carbon::now()->subDays(2),
            ]);

            if ($grv->status === 'in_progress') {
                GrievanceMessage::create([
                    'grievance_id' => $grv->id, 'sender' => 'admin',
                    'message' => "Assigned to {$grv->assigned_to}. Reviewing transaction logs.",
                    'created_at' => Carbon::now()->subDays(1),
                ]);
            } elseif ($grv->status === 'resolved') {
                GrievanceMessage::create([
                    'grievance_id' => $grv->id, 'sender' => 'admin',
                    'message' => "Resolution completed: " . $grv->resolution_note,
                    'created_at' => Carbon::now()->subHours(6),
                ]);
            } elseif ($grv->status === 'escalated') {
                GrievanceMessage::create([
                    'grievance_id' => $grv->id, 'sender' => 'admin',
                    'message' => "[Auto-Escalation Log] " . $grv->resolution_note,
                    'created_at' => Carbon::now()->subHours(12),
                ]);
            }
        }
    }
    
    public function storeEscalationRule(Request $request)
{
    $request->validate([
        'rule_name' => 'required|string|max:255',
        'condition' => 'required|string',
        'action'    => 'required|string',
        'assign_to' => 'required|string',
        'priority'  => 'required|in:low,medium,high',
    ]);

    $rule = EscalationRule::create($request->only(['rule_name', 'condition', 'action', 'assign_to', 'priority']));

    return response()->json(['success' => true, 'message' => 'Rule added successfully', 'rule' => $rule]);
}
}