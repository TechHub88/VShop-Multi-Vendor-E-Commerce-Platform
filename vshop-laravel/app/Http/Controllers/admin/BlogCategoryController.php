<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogCategoryController extends Controller
{
    // Index - List all categories
    public function index(Request $request)
    {
        $query = BlogCategory::query();
        
        // Apply filters
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status == 'active');
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Order by sort order then name
        $categories = $query->orderBy('sort_order')
                           ->orderBy('name')
                           ->paginate(20);
        
        return view('admin.blog-categories.index', [
            'categories' => $categories
        ]);
    }

    // Show create form
    public function create()
    {
        return view('admin.blog-categories.create');
    }

    // Store new category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean'
        ]);

        $category = new BlogCategory();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->sort_order = $request->sort_order ?? 0;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;
        $category->is_active = $request->boolean('is_active');
        
        $category->save();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category created successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $category = BlogCategory::findOrFail($id);
        
        return view('admin.blog-categories.edit', [
            'category' => $category
        ]);
    }

    // Update category
    public function update(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,' . $id,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean'
        ]);

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->sort_order = $request->sort_order ?? $category->sort_order;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;
        $category->is_active = $request->boolean('is_active');
        
        $category->save();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category updated successfully!');
    }

    // Delete category
    public function destroy($id)
    {
        $category = BlogCategory::findOrFail($id);
        
        // Check if category has blogs
        if ($category->blogs()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category. It has ' . $category->blogs()->count() . ' blog posts assigned to it. Reassign them first.');
        }
        
        $category->delete();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category deleted successfully!');
    }

    // Toggle active status (AJAX)
    public function toggleStatus(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        return response()->json([
            'success' => true,
            'is_active' => $category->is_active,
            'message' => 'Category status updated successfully'
        ]);
    }

    // Update sort order (AJAX)
    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:blog_categories,id',
            'categories.*.sort_order' => 'required|integer'
        ]);

        foreach ($request->categories as $item) {
            BlogCategory::where('id', $item['id'])
                       ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sort order updated successfully'
        ]);
    }

    // Bulk actions
    public function bulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:blog_categories,id'
        ]);

        $action = $request->action;
        $ids = $request->ids;
        $count = 0;

        switch ($action) {
            case 'activate':
                BlogCategory::whereIn('id', $ids)->update(['is_active' => true]);
                $count = count($ids);
                break;
                
            case 'deactivate':
                BlogCategory::whereIn('id', $ids)->update(['is_active' => false]);
                $count = count($ids);
                break;
                
            case 'delete':
                // Check for blogs before deleting
                $categoriesWithBlogs = BlogCategory::whereIn('id', $ids)
                    ->has('blogs')
                    ->count();
                    
                if ($categoriesWithBlogs > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete categories that have blog posts assigned'
                    ]);
                }
                
                BlogCategory::whereIn('id', $ids)->delete();
                $count = count($ids);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} categories {$action}d successfully"
        ]);
    }
}