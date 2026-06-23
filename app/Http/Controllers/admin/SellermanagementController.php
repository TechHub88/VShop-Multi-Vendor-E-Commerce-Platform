<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\SellerAuditLog;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

use App\Mail\SellerApprovalMail;

class SellermanagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Seller::query();

        if ($request->status) {
            if ($request->status == 'active') {
                $query->where('is_active', 1);
            } elseif ($request->status == 'inactive') {
                $query->where('is_active', 0);
            } else {
                $query->where('status', $request->status);
            }
        }

        // ADVANCED FILTERS
        if ($request->filter == 'kyc') {
            $query->where(function ($q) {
                $q->where('pan_status', 'pending')
                  ->orWhere('gst_status', 'pending');
            });
        }

        if ($request->filter == 'bank') {
            $query->where('bank_status', 'pending');
        }

        if ($request->filter == 'agreement') {
            $query->where('agreement_status', 'pending');
        }

        $sellers = $query->latest()->paginate(15);

        return view('admin.sellers.index', compact('sellers'));
    }

    public function show($id)
    {
        $seller = Seller::findOrFail($id);
        return view('admin.sellers.show', compact('seller'));
    }

    public function updateVerification(Request $request, $id)
    {
        $seller = Seller::findOrFail($id);
        $oldData = $seller->toArray();
        
        // Update only the existing fields from your model
        $seller->update([
            'status' => $request->status ?? $seller->status,
            'pan_status' => $request->pan_status ?? $seller->pan_status,
            'gst_status' => $request->gst_status ?? $seller->gst_status,
            'iec_status' => $request->iec_status ?? $seller->iec_status,
            'bank_status' => $request->bank_status ?? $seller->bank_status,
            'agreement_status' => $request->agreement_status ?? $seller->agreement_status,
            'company_status' => $request->company_status ?? $seller->company_status,
            'email_verified' => $request->email_verified ?? $seller->email_verified,
        ]);
        
        // Log the update
        $this->logAudit($id, 'kyc_updated', 'KYC/Document verification status updated', $oldData, $seller->toArray());
        
        // AUTO APPROVAL LOGIC - Check if ALL required sections are approved
        $allApproved = (
            ($request->pan_status ?? $seller->pan_status) == 'Approved' &&
            ($request->gst_status ?? $seller->gst_status) == 'Approved' &&
            ($request->iec_status ?? $seller->iec_status) == 'Approved' &&
            ($request->bank_status ?? $seller->bank_status) == 'approved' &&
            ($request->agreement_status ?? $seller->agreement_status) == 'Approved' &&
            ($request->company_status ?? $seller->company_status) == 'approved' &&
            ($request->email_verified ?? $seller->email_verified) == 1
        );
        
        if ($allApproved) {
            $wasApproved = ($oldData['status'] ?? '') === 'approved';
            $seller->status = 'approved';
            $seller->is_verified = 1;
            $seller->save();
            $this->logAudit($id, 'seller_approved', 'Seller automatically approved after all verifications completed');
            NotificationService::notify($seller->id, 'seller', 'kyc', 'Your KYC application has been fully approved by admin.', route('seller.profile'));
            
            if (!$wasApproved) {
                SellerApprovalMail::send($seller->email, $seller->name);
            }
        } else {
            // If not all approved, status remains pending
            if ($seller->status == 'approved') {
                $seller->status = 'pending';
                $seller->is_verified = 0;
                $seller->save();
                $this->logAudit($id, 'status_changed', 'Seller status changed from approved to pending due to incomplete verifications');
            }
        }
        
        return back()->with('success', 'Verification updated successfully!');
    }

    public function approve($id)
    {
        $seller = Seller::findOrFail($id);
        $oldData = $seller->toArray();
        
        $wasApproved = ($oldData['status'] ?? '') === 'approved';
        
        // Approve all statuses at once
        $seller->update([
            'status' => 'approved',
            'pan_status' => 'Approved',
            'gst_status' => 'Approved',
            'iec_status' => 'Approved',
            'bank_status' => 'approved',
            'agreement_status' => 'Approved',
            'company_status' => 'approved',
            'email_verified' => 1,
            'is_verified' => 1,
        ]);
        
        $this->logAudit($id, 'seller_approved', 'Seller fully approved by admin', $oldData, $seller->toArray());
        NotificationService::notify($seller->id, 'seller', 'kyc', 'Your KYC application has been fully approved by admin.', route('seller.profile'));
        
        if (!$wasApproved) {
            SellerApprovalMail::send($seller->email, $seller->name);
        }
        
        return back()->with('success', 'Seller has been fully approved and verified successfully!');
    }

    public function approvePlatforms(Request $request, $id)
    {
        $request->validate([
            'platforms' => 'required|array|min:1'
        ]);

        $seller = Seller::findOrFail($id);
        $oldData = $seller->toArray();
        
        $seller->update([
            'approved_platforms' => $request->platforms
        ]);
        
        $this->logAudit($id, 'platforms_approved', 'Seller platforms approved by admin', $oldData, $seller->toArray());
        NotificationService::notify($seller->id, 'seller', 'platform', 'Your platform access request has been approved by admin.', route('seller.dashboard'));
        
        // Send email using SellerApprovalMail wrapper
        SellerApprovalMail::send($seller->email, $seller->name, 'platforms');
        
        return back()->with('success', 'Selected platforms have been approved for the seller.');
    }

    public function reject($id)
    {
        $seller = Seller::findOrFail($id);
        $oldData = $seller->toArray();
        
        $seller->update([
            'status' => 'rejected',
            'is_verified' => 0,
        ]);
        
        $this->logAudit($id, 'seller_rejected', 'Seller application rejected by admin', $oldData, $seller->toArray());
        NotificationService::notify($seller->id, 'seller', 'kyc', 'Your KYC application has been rejected by admin.', route('seller.profile'));
        
        return back()->with('error', 'Seller has been rejected!');
    }

    // ============ BLACKLIST CONTROL FUNCTIONS ============
    
    public function blacklistIndex(Request $request)
    {
        // Get all blocked sellers (status = 'blocked')
        $blockedSellers = Seller::where('status', 'blocked')->latest()->paginate(20);
        
        // Get blocked products from sellers who have product_blocks
        $allSellers = Seller::whereNotNull('product_blocks')->get();
        $blockedProducts = [];
        foreach ($allSellers as $seller) {
            $productBlocks = json_decode($seller->product_blocks, true) ?? [];
            foreach ($productBlocks as $block) {
                if (isset($block['status']) && $block['status'] == 'blocked') {
                    $block['seller_name'] = $seller->org_name ?? $seller->name;
                    $block['seller_id'] = $seller->id;
                    $blockedProducts[] = $block;
                }
            }
        }
        
        // Get blocked customers
        $blockedCustomers = User::where('status', 'blocked')->latest()->paginate(20);
        
        $type = $request->type;
        
        return view('admin.blacklist.index', compact('blockedSellers', 'blockedProducts', 'blockedCustomers', 'type'));
    }
    
    public function blockSeller(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
            'block_until' => 'nullable|date|after:now',
        ]);
        
        $seller = Seller::findOrFail($id);
        $oldData = $seller->toArray();
        
        // Check if already blocked
        if ($seller->status == 'blocked') {
            return back()->with('error', 'Seller is already blocked!');
        }
        
        // Prepare block metadata
        $blockMetadata = [
            'blocked_by' => Auth::id(),
            'blocked_by_name' => Auth::user()->name,
            'block_type' => 'seller',
            'reason' => $request->reason,
            'block_until' => $request->block_until,
            'blocked_at' => now(),
            'duration' => $request->block_until ? 'temp' : 'permanent'
        ];
        
        // Update seller status to blocked and store metadata
        $seller->update([
            'status' => 'blocked',
            'is_verified' => 0,
            'block_metadata' => json_encode($blockMetadata)
        ]);
        
        $this->logAudit($id, 'seller_blocked', $request->reason, $oldData, $seller->toArray());
        
        return back()->with('success', 'Seller has been blocked successfully!');
    }
    
    public function blockProduct(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
            'seller_id' => 'required|exists:sellers,id',
        ]);
        
        $product = Product::findOrFail($id);
        $seller = Seller::findOrFail($request->seller_id);
        $oldData = $seller->toArray();
        
        // Get existing product blocks
        $productBlocks = json_decode($seller->product_blocks ?? '[]', true);
        
        // Check if product is already blocked
        foreach ($productBlocks as $block) {
            if (isset($block['product_id']) && $block['product_id'] == $id && $block['status'] == 'blocked') {
                return back()->with('error', 'Product is already blocked!');
            }
        }
        
        // Add new product block
        $productBlocks[] = [
            'product_id' => $id,
            'product_name' => $product->name,
            'reason' => $request->reason,
            'blocked_by' => Auth::id(),
            'blocked_by_name' => Auth::user()->name,
            'blocked_at' => now(),
            'status' => 'blocked'
        ];
        
        $seller->update([
            'product_blocks' => json_encode($productBlocks)
        ]);
        
        // Update product status
        $product->update(['status' => 'blocked']);
        
        $this->logAudit($seller->id, 'product_blocked', 'Product blocked: ' . $product->name . ' - Reason: ' . $request->reason, $oldData, $seller->toArray());
        
        return back()->with('success', 'Product has been blocked successfully!');
    }
    
    public function blockCustomer(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);
        
        $customer = User::findOrFail($id);
        $oldData = $customer->toArray();
        
        $customer->update([
            'status' => 'blocked',
            'blocked_at' => now(),
            'blocked_by' => Auth::id(),
            'blocked_by_name' => Auth::user()->name,
            'block_reason' => $request->reason
        ]);
        
        $this->logAudit(null, 'customer_blocked', 'Customer blocked: ' . $customer->email . ' - Reason: ' . $request->reason, $oldData, $customer->toArray());
        
        return back()->with('success', 'Customer has been blocked successfully!');
    }
    
    public function unblock($id)
    {
        return $this->unblockSeller($id);
    }

    public function unblockSeller($id)
    {
        $seller = Seller::findOrFail($id);
        $oldData = $seller->toArray();
        
        $seller->update([
            'status' => 'pending',
            'block_metadata' => null
        ]);
        
        $this->logAudit($id, 'seller_unblocked', 'Seller unblocked by admin', $oldData, $seller->toArray());
        
        return back()->with('success', 'Seller has been unblocked successfully!');
    }
    
    public function unblockProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $seller = Seller::find($product->created_by_id);
        
        if (!$seller) {
            return back()->with('error', 'Seller not found for this product!');
        }
        
        $oldData = $seller->toArray();
        $productBlocks = json_decode($seller->product_blocks ?? '[]', true);
        
        // Remove the product block
        $productBlocks = array_filter($productBlocks, function($block) use ($id) {
            return $block['product_id'] != $id;
        });
        
        $seller->update([
            'product_blocks' => json_encode(array_values($productBlocks))
        ]);
        
        // Update product status
        $product->update(['status' => 'active']);
        
        $this->logAudit($seller->id, 'product_unblocked', 'Product unblocked: ' . $product->name, $oldData, $seller->toArray());
        
        return back()->with('success', 'Product has been unblocked successfully!');
    }
    
    public function unblockCustomer($id)
    {
        $customer = User::findOrFail($id);
        $oldData = $customer->toArray();
        
        $customer->update([
            'status' => 'active',
            'blocked_at' => null,
            'blocked_by' => null,
            'blocked_by_name' => null,
            'block_reason' => null
        ]);
        
        $this->logAudit(null, 'customer_unblocked', 'Customer unblocked: ' . $customer->email, $oldData, $customer->toArray());
        
        return back()->with('success', 'Customer has been unblocked successfully!');
    }
    
    // ============ LOGS & AUDIT FUNCTIONS ============
    
    public function auditLogsIndex(Request $request)
    {
        $query = SellerAuditLog::with('seller', 'user');
        
        if ($request->seller_id) {
            $query->where('seller_id', $request->seller_id);
        }
        
        if ($request->action) {
            $query->where('action', $request->action);
        }
        
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $auditLogs = $query->latest()->paginate(30);
        $sellers = Seller::select('id', 'name', 'org_name', 'email')->get();
        
        return view('admin.audit-logs.index', compact('auditLogs', 'sellers'));
    }
    
    public function sellerTimeline($id)
    {
        $seller = Seller::findOrFail($id);
        $auditLogs = SellerAuditLog::where('seller_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get product blocks from seller's JSON field
        $productBlocks = json_decode($seller->product_blocks ?? '[]', true);
        
        // Get block metadata
        $blockMetadata = json_decode($seller->block_metadata ?? '{}', true);
        
        return view('admin.audit-logs.timeline', compact('seller', 'auditLogs', 'productBlocks', 'blockMetadata'));
    }
    
    public function triggerReVerification($id)
    {
        $seller = Seller::findOrFail($id);
        $oldData = $seller->toArray();
        
        // Reset verification statuses
        $seller->update([
            'pan_status' => 'pending',
            'gst_status' => 'pending',
            'iec_status' => 'pending',
            'bank_status' => 'pending',
            'agreement_status' => 'pending',
            'company_status' => 'pending',
            'status' => 'pending',
            'is_verified' => 0
        ]);
        
        $this->logAudit($id, 'reverification_triggered', 'Admin triggered re-verification', $oldData, $seller->toArray());
        
        return back()->with('warning', 'Re-verification has been triggered for this seller!');
    }
    
    private function logAudit($sellerId, $action, $description, $oldData = null, $newData = null)
    {
        SellerAuditLog::create([
            'seller_id' => $sellerId,
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
    
    public function activate(Request $request)
    {
        try {
            $seller = Seller::find($request->id);
            if (!$seller) {
                return response()->json(['success' => false, 'message' => 'Seller not found']);
            }
            
            $oldData = $seller->toArray();
            
            $seller->is_active = 1;
            $seller->deactivated_reason = null;
            $seller->deactivated_at = null;
            $seller->save();
            
            // Log the activation
            $this->logAudit($seller->id, 'seller_activated', 'Seller activated', $oldData, $seller->toArray());
            
            return response()->json(['success' => true, 'message' => 'Seller activated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deactivate(Request $request)
    {
        try {
            $seller = Seller::find($request->id);
            if (!$seller) {
                return response()->json(['success' => false, 'message' => 'Seller not found']);
            }
            
            $oldData = $seller->toArray();
            
            $seller->is_active = 0;
            $seller->deactivated_reason = $request->reason; // Save reason to seller table
            $seller->deactivated_at = now();
            $seller->save();
            
            // Log the deactivation
            $this->logAudit($seller->id, 'seller_deactivated', 'Seller deactivated with reason: ' . ($request->reason ?? 'No reason provided'), $oldData, $seller->toArray());
            
            return response()->json(['success' => true, 'message' => 'Seller deactivated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}