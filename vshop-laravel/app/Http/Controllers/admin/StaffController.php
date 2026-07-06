<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminStaff;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. STAFF DIRECTORY
    |--------------------------------------------------------------------------
    */

    public function directory(Request $request)
    {
        $query = AdminStaff::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('employee_id', 'like', '%' . $request->search . '%')
                  ->orWhere('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $staffs = $query->latest()->paginate(15);
        
        $totalStaff = AdminStaff::count();
        $activeStaff = AdminStaff::where('status', 'Active')->count();
        $inactiveStaff = AdminStaff::where('status', 'Inactive')->count();
       
    

        return view('admin.staff.directory', compact('staffs', 'totalStaff', 'activeStaff', 'inactiveStaff'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. CREATE STAFF - FORM
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();
        return view('admin.staff.create', compact('roles'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. STORE STAFF - FIXED
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        // Validation rules with proper required fields
        $validated = $request->validate([
            // Required Fields (marked with * in form)
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:admin_staffs,email|max:150',
            'employee_id' => 'required|string|unique:admin_staffs,employee_id|max:50',
            'password' => 'required|string|min:6',
            'role' => 'required|string|max:100',
            'status' => 'required|in:Active,Inactive,Suspended',
            
            // Optional Fields
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'dob' => 'nullable|date|before:today',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'account_holder_name' => 'nullable|string|max:150',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'branch_location' => 'nullable|string|max:150',
            'account_type' => 'nullable|in:Savings,Current,Salary',
            'payroll_notes' => 'nullable|string|max:500',
            'basic_salary' => 'nullable|numeric|min:0|max:9999999.99',
            'hra' => 'nullable|numeric|min:0|max:9999999.99',
            'allowance' => 'nullable|numeric|min:0|max:9999999.99',
            'bonus' => 'nullable|numeric|min:0|max:9999999.99',
            'pf_percent' => 'nullable|string|max:10',
            'professional_tax' => 'nullable|numeric|min:0|max:9999999.99',
            'pan_card_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'cancelled_cheque' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'aadhaar_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'permissions' => 'nullable|array',
        ]);

        try {
            $data = [];
            
            // Required Fields
            $data['first_name'] = $validated['first_name'];
            $data['last_name'] = $validated['last_name'];
            $data['email'] = $validated['email'];
            $data['employee_id'] = $validated['employee_id'];
            $data['password'] = Hash::make($validated['password']);
            $data['role'] = $validated['role'];
            $data['status'] = $validated['status'];
            
            // Optional Fields
            $data['phone'] = $validated['phone'] ?? null;
            $data['gender'] = $validated['gender'] ?? null;
            $data['dob'] = $validated['dob'] ?? null;
            $data['employee_type'] = 'Full Time'; // Default value
            $data['joining_date'] = now(); // Default to current date
            
            // Bank Details
            $data['account_holder_name'] = $validated['account_holder_name'] ?? null;
            $data['bank_name'] = $validated['bank_name'] ?? null;
            $data['account_number'] = $validated['account_number'] ?? null;
            $data['ifsc_code'] = $validated['ifsc_code'] ?? null;
            $data['branch_location'] = $validated['branch_location'] ?? null;
            $data['account_type'] = $validated['account_type'] ?? null;
            $data['payroll_notes'] = $validated['payroll_notes'] ?? null;
            
            // Salary Structure
            $data['basic_salary'] = $validated['basic_salary'] ?? 0;
            $data['hra'] = $validated['hra'] ?? 0;
            $data['allowance'] = $validated['allowance'] ?? 0;
            $data['bonus'] = $validated['bonus'] ?? 0;
            $data['pf_percent'] = $validated['pf_percent'] ?? null;
            $data['professional_tax'] = $validated['professional_tax'] ?? 0;
            
            // Password already handled above

            
            // Profile Photo Upload
            if ($request->hasFile('profile_photo')) {
                $profilePhoto = $request->file('profile_photo');
                $filename = time() . '_profile_' . rand(1000, 9999) . '.' . $profilePhoto->getClientOriginalExtension();
                $uploadPath = public_path('uploads/staff/profile_photos/');
                
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $profilePhoto->move($uploadPath, $filename);
                $data['profile_photo'] = 'uploads/staff/profile_photos/' . $filename;
            }
            
            // Document Uploads
            if ($request->hasFile('pan_card_file')) {
                $panFile = $request->file('pan_card_file');
                $panFilename = time() . '_pan_' . rand(1000, 9999) . '.' . $panFile->getClientOriginalExtension();
                $uploadPath = public_path('uploads/staff/documents/pan/');
                if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);
                $panFile->move($uploadPath, $panFilename);
                $data['pan_card_file'] = 'uploads/staff/documents/pan/' . $panFilename;
            }
            
            if ($request->hasFile('cancelled_cheque')) {
                $chequeFile = $request->file('cancelled_cheque');
                $chequeFilename = time() . '_cheque_' . rand(1000, 9999) . '.' . $chequeFile->getClientOriginalExtension();
                $uploadPath = public_path('uploads/staff/documents/cheques/');
                if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);
                $chequeFile->move($uploadPath, $chequeFilename);
                $data['cancelled_cheque'] = 'uploads/staff/documents/cheques/' . $chequeFilename;
            }
            
            if ($request->hasFile('aadhaar_file')) {
                $aadhaarFile = $request->file('aadhaar_file');
                $aadhaarFilename = time() . '_aadhaar_' . rand(1000, 9999) . '.' . $aadhaarFile->getClientOriginalExtension();
                $uploadPath = public_path('uploads/staff/documents/aadhaar/');
                if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);
                $aadhaarFile->move($uploadPath, $aadhaarFilename);
                $data['aadhaar_file'] = 'uploads/staff/documents/aadhaar/' . $aadhaarFilename;
            }
            
            // Permissions
            if ($request->has('permissions')) {
                $data['permissions'] = json_encode($request->input('permissions'));
            }
            
            $data['created_by'] = auth()->id();
            
            // Create staff record
            $staff = AdminStaff::create($data);
            
            return redirect()
                ->route('admin.staff.directory')
                ->with('success', 'Staff member "' . $staff->first_name . ' ' . $staff->last_name . '" created successfully!');
                
        } catch (\Exception $e) {
            Log::error('Staff creation failed: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create staff member. Error: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 4. EDIT STAFF
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $staff = AdminStaff::findOrFail($id);
        
        if ($staff->permissions && is_string($staff->permissions)) {
            $staff->permissions = json_decode($staff->permissions, true);
        }
        
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();
        return view('admin.staff.edit', compact('staff', 'roles'));
    }

    /*
    |--------------------------------------------------------------------------
    | 5. UPDATE STAFF - FIXED
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $staff = AdminStaff::findOrFail($id);
        
        $validated = $request->validate([
            'first_name' => 'string|max:100',
            'last_name' => 'string|max:100',
            'email' => 'email|unique:admin_staffs,email,' . $id . '|max:150',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'dob' => 'nullable|date|before:today',
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|max:100',
            'status' => 'required|in:Active,Inactive,Suspended',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'account_holder_name' => 'nullable|string|max:150',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'branch_location' => 'nullable|string|max:150',
            'account_type' => 'nullable|in:Savings,Current,Salary',
            'payroll_notes' => 'nullable|string|max:500',
            'basic_salary' => 'nullable|numeric|min:0|max:9999999.99',
            'hra' => 'nullable|numeric|min:0|max:9999999.99',
            'allowance' => 'nullable|numeric|min:0|max:9999999.99',
            'bonus' => 'nullable|numeric|min:0|max:9999999.99',
            'pf_percent' => 'nullable|string|max:10',
            'professional_tax' => 'nullable|numeric|min:0|max:9999999.99',
            'pan_card_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'cancelled_cheque' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'aadhaar_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'permissions' => 'nullable|array',
        ]);
        
        try {
            $data = [];
            
            $data['first_name'] = $validated['first_name'];
            $data['last_name'] = $validated['last_name'];
            $data['email'] = $validated['email'];
            $data['phone'] = $validated['phone'] ?? null;
            $data['gender'] = $validated['gender'] ?? null;
            $data['dob'] = $validated['dob'] ?? null;
            if ($request->filled('password')) {
                $data['password'] = Hash::make($validated['password']);
            }
            $data['role'] = $validated['role'];
            $data['status'] = $validated['status'];
            
            $data['account_holder_name'] = $validated['account_holder_name'] ?? null;
            $data['bank_name'] = $validated['bank_name'] ?? null;
            $data['account_number'] = $validated['account_number'] ?? null;
            $data['ifsc_code'] = $validated['ifsc_code'] ?? null;
            $data['branch_location'] = $validated['branch_location'] ?? null;
            $data['account_type'] = $validated['account_type'] ?? null;
            $data['payroll_notes'] = $validated['payroll_notes'] ?? null;
            
            $data['basic_salary'] = $validated['basic_salary'] ?? 0;
            $data['hra'] = $validated['hra'] ?? 0;
            $data['allowance'] = $validated['allowance'] ?? 0;
            $data['bonus'] = $validated['bonus'] ?? 0;
            $data['pf_percent'] = $validated['pf_percent'] ?? null;
            $data['professional_tax'] = $validated['professional_tax'] ?? 0;
            
            // Profile Photo Upload
            if ($request->hasFile('profile_photo')) {
                if ($staff->profile_photo && file_exists(public_path($staff->profile_photo))) {
                    unlink(public_path($staff->profile_photo));
                }
                
                $profilePhoto = $request->file('profile_photo');
                $filename = time() . '_profile_' . rand(1000, 9999) . '.' . $profilePhoto->getClientOriginalExtension();
                $uploadPath = public_path('uploads/staff/profile_photos/');
                
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $profilePhoto->move($uploadPath, $filename);
                $data['profile_photo'] = 'uploads/staff/profile_photos/' . $filename;
            }
            
            // Document Uploads
            if ($request->hasFile('pan_card_file')) {
                if ($staff->pan_card_file && file_exists(public_path($staff->pan_card_file))) {
                    unlink(public_path($staff->pan_card_file));
                }
                $panFile = $request->file('pan_card_file');
                $panFilename = time() . '_pan_' . rand(1000, 9999) . '.' . $panFile->getClientOriginalExtension();
                $uploadPath = public_path('uploads/staff/documents/pan/');
                if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);
                $panFile->move($uploadPath, $panFilename);
                $data['pan_card_file'] = 'uploads/staff/documents/pan/' . $panFilename;
            }
            
            if ($request->hasFile('cancelled_cheque')) {
                if ($staff->cancelled_cheque && file_exists(public_path($staff->cancelled_cheque))) {
                    unlink(public_path($staff->cancelled_cheque));
                }
                $chequeFile = $request->file('cancelled_cheque');
                $chequeFilename = time() . '_cheque_' . rand(1000, 9999) . '.' . $chequeFile->getClientOriginalExtension();
                $uploadPath = public_path('uploads/staff/documents/cheques/');
                if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);
                $chequeFile->move($uploadPath, $chequeFilename);
                $data['cancelled_cheque'] = 'uploads/staff/documents/cheques/' . $chequeFilename;
            }
            
            if ($request->hasFile('aadhaar_file')) {
                if ($staff->aadhaar_file && file_exists(public_path($staff->aadhaar_file))) {
                    unlink(public_path($staff->aadhaar_file));
                }
                $aadhaarFile = $request->file('aadhaar_file');
                $aadhaarFilename = time() . '_aadhaar_' . rand(1000, 9999) . '.' . $aadhaarFile->getClientOriginalExtension();
                $uploadPath = public_path('uploads/staff/documents/aadhaar/');
                if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);
                $aadhaarFile->move($uploadPath, $aadhaarFilename);
                $data['aadhaar_file'] = 'uploads/staff/documents/aadhaar/' . $aadhaarFilename;
            }
            
            if ($request->has('permissions')) {
                $data['permissions'] = json_encode($request->input('permissions'));
            }
            
            $data['updated_by'] = auth()->id();
            $staff->update($data);
            
            return redirect()->route('admin.staff.directory')
                ->with('success', 'Staff member updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Staff update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update staff member. Please try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 6. SHOW STAFF
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $staff = AdminStaff::findOrFail($id);
        
        if ($staff->permissions && is_string($staff->permissions)) {
            $staff->permissions = json_decode($staff->permissions, true);
        }
        
        $staff->total_salary = ($staff->basic_salary ?? 0) + ($staff->hra ?? 0) + ($staff->allowance ?? 0) + ($staff->bonus ?? 0) - ($staff->professional_tax ?? 0);
        
        return view('admin.staff.show', compact('staff'));
    }

    /*
    |--------------------------------------------------------------------------
    | 7. DESTROY STAFF
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        try {
            $staff = AdminStaff::findOrFail($id);
            
            if ($staff->profile_photo && file_exists(public_path($staff->profile_photo))) {
                unlink(public_path($staff->profile_photo));
            }
            if ($staff->pan_card_file && file_exists(public_path($staff->pan_card_file))) {
                unlink(public_path($staff->pan_card_file));
            }
            if ($staff->aadhaar_file && file_exists(public_path($staff->aadhaar_file))) {
                unlink(public_path($staff->aadhaar_file));
            }
            if ($staff->cancelled_cheque && file_exists(public_path($staff->cancelled_cheque))) {
                unlink(public_path($staff->cancelled_cheque));
            }
            
            $staff->delete();
            
            return response()->json(['success' => true, 'message' => 'Staff deleted successfully']);
            
        } catch (\Exception $e) {
            Log::error('Staff deletion failed: ' . $e->getMessage());
            
            return response()->json(['success' => false, 'message' => 'Failed to delete staff member'], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 8. UPDATE STATUS (AJAX)
    |--------------------------------------------------------------------------
    */

   public function updateStatus(Request $request)
{
    try {
        $request->validate([
            'id' => 'required|exists:admin_staffs,id',
            'status' => 'required|in:Active,Inactive,Suspended'
        ]);
        
        $staff = AdminStaff::find($request->id);
        
        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff not found'
            ], 404);
        }
        
        $staff->status = $request->status;
        $staff->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'new_status' => $staff->status
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    /*
    |--------------------------------------------------------------------------
    | 9. OTHER METHODS (Profile, Bank, Salary, Permissions)
    |--------------------------------------------------------------------------
    */

    public function profile($id)
    {
        $staff = AdminStaff::findOrFail($id);
        return view('admin.staff.profile', compact('staff'));
    }

    public function bankPayroll($id)
    {
        $staff = AdminStaff::findOrFail($id);
        return view('admin.staff.bank-payroll', compact('staff'));
    }

    public function salaryPayments($id)
    {
        $staff = AdminStaff::findOrFail($id);
        return view('admin.staff.salary-payments', compact('staff'));
    }

    public function accessPermissions($id)
    {
        $staff = AdminStaff::findOrFail($id);
        return view('admin.staff.access-permissions', compact('staff'));
    }
}
