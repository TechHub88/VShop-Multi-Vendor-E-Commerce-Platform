<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Grievance;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Services\NotificationService;

class CustomerGrievanceController extends Controller
{
    /**
     * Store a new grievance/complaint.
     */
    public function store(Request $request)
    {
        // 1. Validate
        $validated = $request->validate([
            'order_id'     => 'required|string',
            'issue_type'   => 'required|string|max:100',
            'description'  => 'required|string|min:10',
            'priority'     => 'required|in:Low,Medium,High',
            'product_id'   => 'nullable|exists:products,id',
            'proof.*'      => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,mp4,mov,avi,pdf,doc,docx',
        ]);

        // 2. Get authenticated buyer (customer)
        $buyer = Auth::guard('customer')->user();
        if (!$buyer) {
            return redirect()->route('vshoplogin')->with('error', 'Please login first.');
        }

        // 3. Determine seller from product
        $sellerId = null;
        $productId = $request->input('product_id');
        if ($productId) {
            $product = Product::find($productId);
            if ($product && $product->created_by === 'seller') {
                $sellerId = $product->created_by_id;
            }
        }

        // 4. Store uploaded files in PUBLIC folder
        $attachments = [];
        if ($request->hasFile('proof')) {
            $destinationPath = public_path('uploads/grievances/proofs');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            foreach ($request->file('proof') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $filename);
                $attachments[] = 'uploads/grievances/proofs/' . $filename; // relative path for asset()
            }
        }

        // 5. Generate complaint number
        $complaintNumber = 'CMP-' . strtoupper(Str::random(6)) . date('Ymd');

        // 6. Create grievance record
        $grievance = Grievance::create([
            'buyer_id'         => $buyer->id,
            'seller_id'        => $sellerId,
            'order_id'         => $validated['order_id'],
            'type'             => $validated['issue_type'],
            'subject'          => 'Complaint regarding order #' . $validated['order_id'],
            'message'          => $validated['description'],
            'priority'         => strtolower($validated['priority']),
            'status'           => 'open',
            'assigned_to'      => null,
            'complaint_number' => $complaintNumber,
            'attachments'      => json_encode($attachments), // store as JSON
        ]);

        NotificationService::notify(
            'all',
            'admin',
            'grievance',
            "New grievance raised by customer {$buyer->name}.",
            route('admin.grievances.show', $grievance->id)
        );

        if ($sellerId) {
            NotificationService::notify(
                $sellerId,
                'seller',
                'grievance',
                "A customer raised a grievance regarding order #{$validated['order_id']}.",
                route('seller.grievance.list')
            );
        }

        // 7. Return response
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Complaint raised successfully!',
                'complaint_id' => $grievance->id,
                'complaint_number' => $complaintNumber,
            ]);
        }

        return redirect()->back()->with('success', 'Complaint raised successfully! Reference ID: ' . $complaintNumber);
    }

    /**
     * Fetch complaint details for AJAX modal.
     */
    public function details(Request $request)
    {
        $id = $request->input('id');
        $complaint = Grievance::where('buyer_id', Auth::guard('customer')->id())
            ->where('id', $id)
            ->firstOrFail();

        // Decode attachments to array
        $attachments = json_decode($complaint->attachments, true) ?? [];

        // Generate full URLs for attachments
        $attachmentUrls = array_map(function($path) {
            return asset($path);
        }, $attachments);

        // Prepare data for the modal
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $complaint->id,
                'complaint_number' => $complaint->complaint_number ?? 'CMP-' . $complaint->id,
                'status' => $complaint->status,
                'order_id' => $complaint->order_id,
                'type' => $complaint->type,
                'priority' => $complaint->priority,
                'created_at' => $complaint->created_at,
                'assigned_at' => $complaint->assigned_at ?? null,
                'investigation_started_at' => $complaint->investigation_started_at ?? null,
                'resolved_at' => $complaint->resolved_at ?? null,
                'attachments' => $attachmentUrls, // return full URLs
                'message' => $complaint->message,
                'subject' => $complaint->subject,
            ]
        ]);
    }
}
