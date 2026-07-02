<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SizeGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display categories based on user type
     */
    public function index()
    {
        $user = auth()->user();
        $userType = $user->type; // CORRECT: user table ka 'type' column
        
        // Log for debugging
        \Log::info("Category Controller - User ID: {$user->id}, User Type: {$userType}");
        
        // Determine which categories to show
        if ($userType == 'product') {
            // Product admin should see ONLY product categories
            $categories = Category::where('type', 'product')->get();
            
            \Log::info("Product Admin - Found {$categories->count()} product categories");
            
        } elseif ($userType == 'travel') {
            // Travel admin should see ONLY travel categories
            $categories = Category::where('type', 'travel')->get();
            
            \Log::info("Travel Admin - Found {$categories->count()} travel categories");
            
        } else {
            // Super admin sees all
            $categories = Category::all();
            
            \Log::info("Admin - Showing all {$categories->count()} categories");
        }
        
        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show form to create new category
     */
    public function create()
    {
        $user = auth()->user();
        $userType = $user->type;
        
        return view('admin.category.create', compact('userType'));
    }

    /**
     * Store a new category
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $userType = $user->type;
        
        // If user is product/travel admin, force the type
        $type = $request->type;
        if ($userType == 'product' || $userType == 'travel') {
            $type = $userType; // Force type based on user
        }
        
        $request->validate([
            'name' => 'required|unique:categories,name',
            'title' => 'required',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        
        // Additional validation for travel categories
        if ($type == 'travel') {
            $request->validate([
                'tour_type' => 'required|in:domestic,international',
                'ideal_for' => 'required',
                'duration_range' => 'required',
                'best_season' => 'required',
            ]);
        }

        $bannerImage = null;
        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/category_banners'), $filename);
            $bannerImage = 'uploads/category_banners/'.$filename;
        }
        
        // Size guide image for product categories
        $sizeGuideImage = null;
        if ($type == 'product' && $request->hasFile('size_guide_image')) {
            $file = $request->file('size_guide_image');
            $filename = time().'_size_guide_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/size_guides'), $filename);
            $sizeGuideImage = 'uploads/size_guides/'.$filename;
        }

        // Create category
        $category = Category::create([
            'type' => $type,
            'name' => $request->name,
            'title' => $request->title,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->name),
            'description' => $request->description,
            'tour_type' => $request->tour_type,
            'ideal_for' => $request->ideal_for,
            'duration_range' => $request->duration_range,
            'best_season' => $request->best_season,
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
            'featured' => $request->featured ?? 0,
            'show_in_store' => $request->show_in_store ?? ($type == 'product' ? 1 : 0),
            'status' => $request->status === 'active' ? 1 : 0,
            'banner_image' => $bannerImage,
            'size_guide_image' => $sizeGuideImage,
        ]);
        
        // Create size guides for product categories
        if ($type == 'product' && $request->has('size_guides')) {
            foreach ($request->size_guides as $sizeData) {
                if (!empty($sizeData['size_label']) && !empty($sizeData['chest'])) {
                    SizeGuide::create([
                        'category_id' => $category->id,
                        'unit' => $sizeData['unit'] ?? 'in',
                        'size_label' => $sizeData['size_label'],
                        'chest' => $sizeData['chest'],
                        'front_length' => $sizeData['front_length'],
                        'sleeve_length' => $sizeData['sleeve_length'],
                    ]);
                }
            }
        }

        $typeLabel = ucfirst($type);
        return redirect()->route('admin.categories.index')
            ->with('success', $typeLabel . ' Category Created Successfully');
    }

    /**
     * Show form to edit category
     */
    public function edit($id)
    {
        $user = auth()->user();
        $userType = $user->type;
        
        $category = Category::with('sizeGuides')->findOrFail($id);
        
        // Check if user has permission to edit this category
        if (($userType == 'product' && $category->type != 'product') || 
            ($userType == 'travel' && $category->type != 'travel')) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'You do not have permission to edit this category.');
        }
        
        return view('admin.category.edit', compact('category', 'userType'));
    }

    /**
     * Update category
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check if user has permission to update this category
        if (($userType == 'product' && $category->type != 'product') || 
            ($userType == 'travel' && $category->type != 'travel')) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'You do not have permission to update this category.');
        }
        
        // Determine type - product/travel admins can't change type
        $type = $category->type;
        if ($userType == 'admin') {
            $type = $request->type;
        }

        $request->validate([
            'name' => 'required|unique:categories,name,' . $id,
            'title' => 'required',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        
        // Additional validation for travel categories
        if ($type == 'travel') {
            $request->validate([
                'tour_type' => 'required|in:domestic,international',
                'ideal_for' => 'required',
                'duration_range' => 'required',
                'best_season' => 'required',
            ]);
        }

        // Update banner image if provided
        if ($request->hasFile('banner_image')) {
            if ($category->banner_image && file_exists(public_path($category->banner_image))) {
                unlink(public_path($category->banner_image));
            }
            $file = $request->file('banner_image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/category_banners'), $filename);
            $category->banner_image = 'uploads/category_banners/'.$filename;
        }
        
        // Update size guide image for product categories
        if ($type == 'product' && $request->hasFile('size_guide_image')) {
            if ($category->size_guide_image && file_exists(public_path($category->size_guide_image))) {
                unlink(public_path($category->size_guide_image));
            }
            $file = $request->file('size_guide_image');
            $filename = time().'_size_guide_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/size_guides'), $filename);
            $category->size_guide_image = 'uploads/size_guides/'.$filename;
        }

        // Update category
        $category->update([
            'type' => $type,
            'name' => $request->name,
            'title' => $request->title,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->name),
            'description' => $request->description,
            'tour_type' => $request->tour_type,
            'ideal_for' => $request->ideal_for,
            'duration_range' => $request->duration_range,
            'best_season' => $request->best_season,
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
            'featured' => $request->featured ?? 0,
            'show_in_store' => $request->show_in_store ?? ($type == 'product' ? 1 : 0),
            'status' => $request->status === 'active' ? 1 : 0,
        ]);
        
        // Update size guides for product categories
        if ($type == 'product' && $request->has('size_guides')) {
            // Delete existing size guides
            SizeGuide::where('category_id', $id)->delete();
            
            // Create new size guides
            foreach ($request->size_guides as $sizeData) {
                if (!empty($sizeData['size_label']) && !empty($sizeData['chest'])) {
                    SizeGuide::create([
                        'category_id' => $category->id,
                        'unit' => $sizeData['unit'] ?? 'in',
                        'size_label' => $sizeData['size_label'],
                        'chest' => $sizeData['chest'],
                        'front_length' => $sizeData['front_length'],
                        'sleeve_length' => $sizeData['sleeve_length'],
                    ]);
                }
            }
        }

        $typeLabel = ucfirst($type);
        return redirect()->route('admin.categories.index')
            ->with('success', $typeLabel . ' Category Updated Successfully');
    }

    /**
     * Delete category
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check if user has permission to delete this category
        if (($userType == 'product' && $category->type != 'product') || 
            ($userType == 'travel' && $category->type != 'travel')) {
            return back()->with('error', 'You do not have permission to delete this category.');
        }
        
        // Delete banner image if exists
        if ($category->banner_image && file_exists(public_path($category->banner_image))) {
            unlink(public_path($category->banner_image));
        }
        
        // Delete size guide image if exists
        if ($category->size_guide_image && file_exists(public_path($category->size_guide_image))) {
            unlink(public_path($category->size_guide_image));
        }
        
        // Delete size guides
        SizeGuide::where('category_id', $id)->delete();
        
        // Delete category
        $category->delete();

        return back()->with('success', 'Category Deleted Successfully');
    }

    /**
     * Search categories (AJAX)
     */
    public function search(Request $request)
    {
        $user = auth()->user();
        $userType = $user->type; // CORRECTED: user table ka 'type' column
        
        $query = Category::query();
        
        // Role-based filtering
        if ($userType == 'travel') {
            $query->where('type', 'travel');
        } elseif ($userType == 'product') {
            $query->where('type', 'product');
        }
        // Admin sees all (no filter)
        
        // Apply filters
        if ($request->keyword) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%')
                  ->orWhere('title', 'like', '%' . $request->keyword . '%');
            });
        }
        
        if ($request->name) {
            $query->where('name', $request->name);
        }
        
        if ($request->type && $userType == 'admin') {
            $query->where('type', $request->type);
        }
        
        if ($request->tour_type && ($userType == 'travel' || $userType == 'admin')) {
            $query->where('tour_type', $request->tour_type);
        }
        
        if ($request->status) {
            $query->where('status', $request->status == 'active' ? 1 : 0);
        }
        
        if ($request->featured) {
            $query->where('featured', $request->featured == 'featured' ? 1 : 0);
        }
        
        if ($request->has('product_count_filter') && $userType == 'product') {
            // Add product count filter logic here if needed
        }
        
        // Apply sorting
        switch($request->sort_filter) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
        }
        
        $categories = $query->get();
        
        // Format response
        $formatted = $categories->map(function($cat) use ($userType) {
            $item = [
                'id' => $cat->id,
                'name' => $cat->name,
                'type' => $cat->type,
                'tour_type' => $cat->tour_type,
                'ideal_for' => $cat->ideal_for,
                'duration_range' => $cat->duration_range,
                'best_season' => $cat->best_season,
                'show_in_store' => $cat->show_in_store,
                'featured' => $cat->featured,
                'status' => $cat->status,
                'created_at_formatted' => $cat->created_at->format('d M, Y'),
                'edit_url' => route('admin.categories.edit', $cat->id),
                'delete_url' => route('admin.categories.destroy', $cat->id),
            ];
            
            // Add appropriate view URL based on type
            if ($cat->type == 'travel') {
                $item['tours_url'] = route('admin.tours.index', ['category_id' => $cat->id]);
            } else {
                $item['products_url'] = route('admin.products.index', ['category_id' => $cat->id]);
            }
            
            return $item;
        });
        
        return response()->json([
            'success' => true,
            'data' => $formatted,
            'message' => 'Categories retrieved successfully'
        ]);
    }
}