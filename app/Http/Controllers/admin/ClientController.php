<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\User;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
   
public function index(Request $request)
{
    $userType = Auth::user()->type;
    $brandType = ($userType == 'travel') ? 'location' : 'brand';
    
    // Get all brands with product count using correct column name
    $clients = Brand::withCount(['products' => function($query) {
        // Count only active products if needed
        // $query->where('status', 'active');
    }])
    ->where('type', $brandType) // Filter by type
    ->orderBy('created_at', 'desc')
    ->get();

    // If you want to filter by search
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $clients = Brand::withCount('products')
            ->where('type', $brandType) // Filter by type
            ->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    return view('admin.clients.index', compact('clients'));
}
    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $userType = Auth::user()->type;
        $brandType = ($userType == 'travel') ? 'location' : 'brand';
        
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $destinationPath = public_path('uploads/files');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0775, true);
            }

            // 🔥 KEEP ORIGINAL FILENAME (important for flags)
            $fileName = $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);

            $filePath = 'uploads/files/' . $fileName;
        }

        Brand::create([
            'name' => $request->name,
            'file' => $filePath,
            'type' => $brandType, // Set type based on user
        ]);

        // Redirect to appropriate route based on user type
        $redirectRoute = ($userType == 'travel') 
            ? 'admin.location.index' 
            : 'admin.clients.index';
            
        return redirect()
            ->route($redirectRoute)
            ->with('success', ($userType == 'travel' ? 'Location' : 'Brand') . ' created successfully');
    }

    public function edit($id)
    {
        $userType = Auth::user()->type;
        $brandType = ($userType == 'travel') ? 'location' : 'brand';
        
        // Ensure user can only edit their own type
        $client = Brand::where('id', $id)
                      ->where('type', $brandType)
                      ->firstOrFail();

        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, $id)
    {
        $userType = Auth::user()->type;
        $brandType = ($userType == 'travel') ? 'location' : 'brand';
        
        // Ensure user can only update their own type
        $brand = Brand::where('id', $id)
                     ->where('type', $brandType)
                     ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $filePath = $brand->file; // keep old logo by default

        if ($request->hasFile('file')) {
            // delete old file if exists
            if ($brand->file && file_exists(public_path($brand->file))) {
                unlink(public_path($brand->file));
            }

            $file = $request->file('file');

            $destinationPath = public_path('uploads/files');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0775, true);
            }

            // 🔥 KEEP ORIGINAL FILENAME
            $fileName = $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);

            $filePath = 'uploads/files/' . $fileName;
        }

        $brand->update([
            'name' => $request->name,
            'file' => $filePath,
            // Type remains the same
        ]);

        // Redirect to appropriate route based on user type
        $redirectRoute = ($userType == 'travel') 
            ? 'admin.location.index' 
            : 'admin.clients.index';
            
        return redirect()
            ->route($redirectRoute)
            ->with('success', ($userType == 'travel' ? 'Location' : 'Brand') . ' updated successfully');
    }

    public function destroy($id)
    {
        $userType = Auth::user()->type;
        $brandType = ($userType == 'travel') ? 'location' : 'brand';
        
        // Ensure user can only delete their own type
        $client = Brand::where('id', $id)
                      ->where('type', $brandType)
                      ->firstOrFail();

        // Optional: delete related institution details if needed
        $client->institutionDetails()->delete();

        $client->delete();

        // Redirect to appropriate route based on user type
        $redirectRoute = ($userType == 'travel') 
            ? 'admin.location.index' 
            : 'admin.clients.index';
            
        return redirect()->route($redirectRoute)
                         ->with('success', ($userType == 'travel' ? 'Location' : 'Brand') . ' deleted successfully.');
    }

    public function brandProducts(Request $request)
    {
        $userType = Auth::user()->type;
        $brandType = ($userType == 'travel') ? 'location' : 'brand';
        
        // Fetch brands based on user type
        $brands = Brand::where('type', $brandType)
                      ->select('id', 'name')
                      ->get();

        // Prepare products per brand
        $brandProducts = [];

        foreach ($brands as $brand) {
            $brandProducts[$brand->id] = Product::where('brand', $brand->id)
                ->with('attributes.term')
                ->paginate(3)
                ->withQueryString();
        }

        return view('admin.brandproducts', compact('brands', 'brandProducts'));
    }

    public function branchcreate()
    {
        $userType = Auth::user()->type;
        $brandType = ($userType == 'travel') ? 'location' : 'brand';
        
        // Fetch brands based on user type
        $brands = Brand::where('type', $brandType)->get();

        return view('admin.clients.branchcreate', compact('brands'));
    }

    public function brachstore(Request $request)
    {
        $userType = Auth::user()->type;
        
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:10',
        ]);

        // Check if brand exists and matches user type
        $brandType = ($userType == 'travel') ? 'location' : 'brand';
        $brand = Brand::where('id', $request->brand_id)
                     ->where('type', $brandType)
                     ->firstOrFail();

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'manager',
        ]);

        // Create the branch
        Branch::create([
            'brand_id' => $brand->id,
            'user_id' => $user->id,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
        ]);

        return redirect()->route('admin.brand-location.create')
                         ->with('success', 'Branch created successfully');
    }

    public function search(Request $request)
    {
        try {
            $userType = Auth::user()->type;
            $brandType = ($userType == 'travel') ? 'location' : 'brand';
            
            $query = Brand::withCount('products')
                         ->where('type', $brandType); // Filter by type

            // Keyword search
            if ($request->keyword) {
                $keyword = $request->keyword;
                $query->where('name', 'like', "%{$keyword}%");
            }
            
            // Name exact match filter
            if ($request->brand_name) {
                $query->where('name', $request->brand_name);
            }
            
            // Logo filter
            if ($request->logo_filter) {
                if ($request->logo_filter === 'with_logo') {
                    $query->whereNotNull('file')->where('file', '!=', '');
                } elseif ($request->logo_filter === 'without_logo') {
                    $query->where(function($q) {
                        $q->whereNull('file')->orWhere('file', '');
                    });
                }
            }
            
            // Product count filter (only for product admin)
            if ($request->product_count_filter && $userType == 'product') {
                $filter = $request->product_count_filter;
                if ($filter == 'has_products') {
                    $query->has('products');
                } elseif ($filter == 'no_products') {
                    $query->doesntHave('products');
                } elseif ($filter == '5+') {
                    $query->having('products_count', '>=', 5);
                } elseif ($filter == '10+') {
                    $query->having('products_count', '>=', 10);
                } elseif ($filter == '20+') {
                    $query->having('products_count', '>=', 20);
                }
            }
            
            // Sort filter
            if ($request->sort_filter) {
                switch ($request->sort_filter) {
                    case 'newest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $query->orderBy('created_at', 'asc');
                        break;
                    case 'name_asc':
                        $query->orderBy('name', 'asc');
                        break;
                    case 'name_desc':
                        $query->orderBy('name', 'desc');
                        break;
                    case 'products_asc':
                        $query->orderBy('products_count', 'asc');
                        break;
                    case 'products_desc':
                        $query->orderBy('products_count', 'desc');
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            // Determine route names based on user type
            $editRoute = ($userType == 'travel') ? 'admin.location.edit' : 'admin.clients.edit';
            $destroyRoute = ($userType == 'travel') ? 'admin.location.destroy' : 'admin.clients.destroy';
            
            // For export, return all data
            if ($request->export) {
                $brands = $query->get()->map(function($brand) use ($editRoute, $destroyRoute, $userType) {
                    return [
                        'name' => $brand->name,
                        'products_count' => $brand->products_count ?? 0,
                        'logo_url' => $brand->file ? asset($brand->file) : null,
                        'created_at_formatted' => $brand->created_at ? $brand->created_at->format('d M, Y') : null,
                        'updated_at_formatted' => $brand->updated_at ? $brand->updated_at->format('d M, Y') : null,
                        'edit_url' => route($editRoute, $brand->id),
                        'delete_url' => route($destroyRoute, $brand->id),
                        'products_url' => route('admin.products.index', ['brand_id' => $brand->id])
                    ];
                });
            } else {
                // For table display
                $brands = $query->get();
                
                // Transform for table display
                $brands = $brands->map(function($brand, $index) use ($editRoute, $destroyRoute, $userType) {
                    return [
                        'id' => $brand->id,
                        'name' => $brand->name,
                        'products_count' => $brand->products_count ?? 0,
                        'logo_url' => $brand->file ? asset($brand->file) : null,
                        'created_at_formatted' => $brand->created_at ? $brand->created_at->format('d M, Y') : null,
                        'updated_at_formatted' => $brand->updated_at ? $brand->updated_at->format('d M, Y') : null,
                        'edit_url' => route($editRoute, $brand->id),
                        'delete_url' => route($destroyRoute, $brand->id),
                        'products_url' => route('admin.products.index', ['brand_id' => $brand->id])
                    ];
                });
            }
            
            return response()->json([
                'success' => true,
                'data' => $brands
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}