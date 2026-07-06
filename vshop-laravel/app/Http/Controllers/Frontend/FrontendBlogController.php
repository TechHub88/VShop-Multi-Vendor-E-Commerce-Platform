<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\About;
use App\Models\Category;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\Footer;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;



class FrontendBlogController extends Controller
{
    
    
    public function bloglisting(Request $request)
    {
        // Get published blogs with category relationship
        $query = Blog::with(['category', 'author'])
            ->where('status', 'published')
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
        
        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('blog_categories.slug', $request->category);
            });
        }
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        $blogs = $query->orderBy('published_at', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->paginate(9);
        
        // Get blog categories for sidebar/filter
        $blogCategories = BlogCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        // Other data
        $about = About::first();
        $categories = Category::all();
        $footer = Footer::first();
        
        return view('frontend.blog.index', compact('blogs', 'about', 'categories', 'footer', 'blogCategories'));
    }

    // Blog Details
    public function blogdetails($slug)
    {
        $blog = Blog::with(['category', 'author'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        
        // Increment view count
        $blog->increment('view_count');
        
        // Get active comments (only top-level, replies will be loaded via relationship)
        $comments = BlogComment::with('replies')
            ->where('blog_id', $blog->id)
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get comment count
        $commentCount = BlogComment::where('blog_id', $blog->id)
            ->where('status', 'active')
            ->count();
        
        // Get related blogs (same category, excluding current)
        $relatedBlogs = Blog::with(['category', 'author'])
            ->where('status', 'published')
            ->where('id', '!=', $blog->id)
            ->where(function($q) use ($blog) {
                if ($blog->category_id) {
                    $q->where('category_id', $blog->category_id);
                }
            })
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        $about = About::first();
        $categories = Category::all();
        $footer = Footer::first();
        
        return view('frontend.blog.details', compact('blog', 'about', 'categories', 'footer', 'relatedBlogs', 'comments', 'commentCount'));
    }

    // Store Comment
    public function storeComment(Request $request, $slug)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'comment' => 'required|string|max:65525',
            'parent_id' => 'nullable|integer|exists:blog_comments,id',
        ]);

        $blog = Blog::where('slug', $slug)->firstOrFail();

        $blogComment = new BlogComment();
        $blogComment->blog_id = $blog->id;
        $blogComment->name = $request->name;
        $blogComment->email = $request->email;
        $blogComment->website = $request->website;
        $blogComment->comment = $request->comment;
        $blogComment->status = 'inactive'; // Set status as inactive initially
        
        // Only set parent_id if it's provided and not 0 or empty
        if ($request->has('parent_id') && $request->parent_id && $request->parent_id != '0' && $request->parent_id != 0) {
            $blogComment->parent_id = $request->parent_id;
        } else {
            $blogComment->parent_id = null;
        }
        
        $blogComment->save();

        return redirect()->to(route('blogs.details', $slug) . '#comments-section')
            ->with('success', 'Your comment has been submitted and is pending approval.');
    }
}
