<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    // Blog statuses
    private $statuses = ['draft', 'published', 'archived'];

    // Index - List all blogs
    public function index(Request $request)
    {
        $query = Blog::query()
            ->leftJoin('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
            ->select('blogs.*', 'blog_categories.name as category_name');
        
        // Apply filters
        if ($request->has('status') && $request->status != '') {
            $query->where('blogs.status', $request->status);
        }
        
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('blogs.category_id', $request->category_id);
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('blogs.title', 'like', "%{$search}%")
                  ->orWhere('blogs.excerpt', 'like', "%{$search}%")
                  ->orWhere('blogs.content', 'like', "%{$search}%");
            });
        }
        
        // Get categories for filter dropdown
        $categories = BlogCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        // Order by latest first
        $blogs = $query->with(['author', 'comments'])
                      ->withCount('comments')
                      ->orderBy('blogs.created_at', 'desc')
                      ->paginate(15);
        
        return view('admin.blogs.index', [
            'blogs' => $blogs,
            'categories' => $categories,
            'statuses' => $this->statuses
        ]);
    }

    // Show blog comments
    public function comments($id)
    {
        $blog = Blog::findOrFail($id);
        
        $comments = BlogComment::where('blog_id', $id)
            ->with('parent')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.blogs.comments', [
            'blog' => $blog,
            'comments' => $comments
        ]);
    }

    // Update comment status
    public function updateCommentStatus(Request $request, $id, $commentId)
    {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $comment = BlogComment::where('id', $commentId)
            ->where('blog_id', $id)
            ->firstOrFail();
        
        $comment->status = $request->status;
        $comment->save();

        return response()->json([
            'success' => true,
            'status' => $comment->status,
            'message' => 'Comment status updated successfully'
        ]);
    }

    // Delete comment
    public function deleteComment($id, $commentId)
    {
        $comment = BlogComment::where('id', $commentId)
            ->where('blog_id', $id)
            ->firstOrFail();
        
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }

    // Show create form
    public function create()
    {
        $categories = BlogCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return view('admin.blogs.create', [
            'categories' => $categories,
            'statuses' => $this->statuses
        ]);
    }

    // Store new blog
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:blog_categories,id', // Changed to category_id
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
            'tags' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $blog = new Blog();
        $blog->title = $request->title;
        $blog->slug = Str::slug($request->title);
        $blog->author_id = Auth::id();
        $blog->category_id = $request->category_id; // Changed
        $blog->excerpt = $request->excerpt;
        $blog->content = $request->content;
        $blog->meta_title = $request->meta_title;
        $blog->meta_description = $request->meta_description;
        $blog->meta_keywords = $request->meta_keywords;
        $blog->tags = $request->tags ? explode(',', $request->tags) : null;
        $blog->status = $request->status;
        $blog->is_featured = $request->boolean('is_featured');
        $blog->published_at = $request->published_at;

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/blog-images'), $imageName);
            $blog->featured_image = 'uploads/blog-images/' . $imageName;
        }

        $blog->save();

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog post created successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $blog = Blog::with('category')->findOrFail($id);
        $categories = BlogCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return view('admin.blogs.edit', [
            'blog' => $blog,
            'categories' => $categories,
            'statuses' => $this->statuses
        ]);
    }

    // Update blog
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:blog_categories,id', // Changed to category_id
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
            'tags' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $blog = Blog::findOrFail($id);
        $blog->title = $request->title;
        $blog->category_id = $request->category_id; // Changed
        $blog->excerpt = $request->excerpt;
        $blog->content = $request->content;
        $blog->meta_title = $request->meta_title;
        $blog->meta_description = $request->meta_description;
        $blog->meta_keywords = $request->meta_keywords;
        $blog->tags = $request->tags ? explode(',', $request->tags) : null;
        $blog->status = $request->status;
        $blog->is_featured = $request->boolean('is_featured');
        $blog->published_at = $request->published_at;

        // Handle featured image update
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($blog->featured_image && file_exists(public_path($blog->featured_image))) {
                unlink(public_path($blog->featured_image));
            }
            
            $image = $request->file('featured_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/blog-images'), $imageName);
            $blog->featured_image = 'uploads/blog-images/' . $imageName;
        }

        $blog->save();

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog post updated successfully!');
    }

    // Delete blog
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        
        // Delete featured image if exists
        if ($blog->featured_image && file_exists(public_path($blog->featured_image))) {
            unlink(public_path($blog->featured_image));
        }
        
        $blog->delete();

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog post deleted successfully!');
    }

    // Toggle featured status (AJAX)
    public function toggleFeatured(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $blog->is_featured = !$blog->is_featured;
        $blog->save();

        return response()->json([
            'success' => true,
            'is_featured' => $blog->is_featured,
            'message' => 'Featured status updated successfully'
        ]);
    }

    // Update status (AJAX)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,published,archived'
        ]);

        $blog = Blog::findOrFail($id);
        $blog->status = $request->status;
        
        // If publishing for first time, set publish date
        if ($request->status === 'published' && !$blog->published_at) {
            $blog->published_at = now();
        }
        
        $blog->save();

        return response()->json([
            'success' => true,
            'status' => $blog->status,
            'message' => 'Status updated successfully'
        ]);
    }
}