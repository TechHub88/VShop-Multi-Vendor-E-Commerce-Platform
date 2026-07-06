<?php
// app/Http/Controllers/Admin/PageController.php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DynamicPage;
use App\Models\PageImage;
use App\Models\MarketplaceCategory;
use App\Models\Seller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    
    
    
    
    public function index()
    {
        $pages = DynamicPage::orderBy('id', 'desc')->paginate(15);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $categories = MarketplaceCategory::where('status', 1)->get();
        $sellers = Seller::all();
        $products = Product::where('status', 1)->get();
        return view('admin.pages.create', compact('categories', 'sellers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9-]+$/|unique:dynamic_pages,slug',
            'content' => 'required|string',
            'status' => 'required|in:draft,pending,approved,published,rejected',
            'page_type' => 'nullable|string',
            'template' => 'nullable|string',
            'app_type' => 'nullable|in:all,vshop,vtravel,vedge,vcampus',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'robots' => 'nullable|string',
            'og_title' => 'nullable|string',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image|max:2048',
            'twitter_card' => 'nullable|string',
            'twitter_title' => 'nullable|string',
            'twitter_description' => 'nullable|string',
            'twitter_image' => 'nullable|image|max:2048',
            'schema_markup' => 'nullable|string',
            'sections' => 'nullable|string',
            'blocks' => 'nullable|string',
            'travel_price' => 'nullable|numeric',
            'travel_duration' => 'nullable|string',
            'travel_inclusions' => 'nullable|string',
            'travel_exclusions' => 'nullable|string',
            'mapped_sellers' => 'nullable|array',
            'mapped_categories' => 'nullable|array',
            'mapped_products' => 'nullable|array',
            'cta_text' => 'nullable|string',
            'cta_link' => 'nullable|url',
            'cta_position' => 'nullable|in:top,middle,bottom',
            'secondary_cta_text' => 'nullable|string',
            'secondary_cta_link' => 'nullable|url',
            'analytics_id' => 'nullable|string',
            'track_conversions' => 'boolean',
            'conversion_goals' => 'nullable|json',
            'enable_ai_blocks' => 'boolean',
            'ai_recommendation_type' => 'nullable|in:related,popular,personalized',
            'auto_internal_linking' => 'boolean',
            'enable_smart_suggestions' => 'boolean',
            'is_featured' => 'boolean',
            'is_sponsored' => 'boolean',
            'sponsored_until' => 'nullable|date',
            'featured_order' => 'nullable|integer',
            'scheduled_publish_at' => 'nullable|date',
            'rejection_reason' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Generate unique slug
            $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);
            $original = $slug;
            $counter = 1;
            while (DynamicPage::where('slug', $slug)->exists()) {
                $slug = $original . '-' . $counter++;
            }

            // Handle file uploads
            $featuredImage = $request->hasFile('featured_image') 
                ? $request->file('featured_image')->store('pages', 'public') 
                : null;
            $ogImage = $request->hasFile('og_image') 
                ? $request->file('og_image')->store('pages/og', 'public') 
                : null;
            $twitterImage = $request->hasFile('twitter_image') 
                ? $request->file('twitter_image')->store('pages/twitter', 'public') 
                : null;

            $page = DynamicPage::create([
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'excerpt' => $request->excerpt,
                'featured_image' => $featuredImage,
                'status' => $request->status,
                'page_type' => $request->page_type ?? 'standard',
                'template' => $request->template ?? 'default',
                'app_type' => $request->app_type ?? 'all',
                'seo_title' => $request->seo_title,
                'seo_description' => $request->seo_description,
                'seo_keywords' => $request->seo_keywords,
                'canonical_url' => $request->canonical_url,
                'robots' => $request->robots ?? 'index,follow',
                'og_title' => $request->og_title,
                'og_description' => $request->og_description,
                'og_image' => $ogImage,
                'twitter_card' => $request->twitter_card,
                'twitter_title' => $request->twitter_title,
                'twitter_description' => $request->twitter_description,
                'twitter_image' => $twitterImage,
                'schema_markup' => $request->schema_markup,
                'sections' => $request->sections,
                'blocks' => $request->blocks,
                'travel_price' => $request->travel_price,
                'travel_duration' => $request->travel_duration,
                'travel_inclusions' => $request->travel_inclusions,
                'travel_exclusions' => $request->travel_exclusions,
                'mapped_sellers' => $request->mapped_sellers ? array_values($request->mapped_sellers) : null,
                'mapped_categories' => $request->mapped_categories ? array_values($request->mapped_categories) : null,
                'mapped_products' => $request->mapped_products ? array_values($request->mapped_products) : null,
                'cta_text' => $request->cta_text,
                'cta_link' => $request->cta_link,
                'cta_position' => $request->cta_position,
                'secondary_cta_text' => $request->secondary_cta_text,
                'secondary_cta_link' => $request->secondary_cta_link,
                'analytics_id' => $request->analytics_id,
                'track_conversions' => $request->track_conversions ?? false,
                'conversion_goals' => $request->conversion_goals,
                'enable_ai_blocks' => $request->enable_ai_blocks ?? false,
                'ai_recommendation_type' => $request->ai_recommendation_type,
                'auto_internal_linking' => $request->auto_internal_linking ?? false,
                'enable_smart_suggestions' => $request->enable_smart_suggestions ?? false,
                'is_featured' => $request->is_featured ?? false,
                'is_sponsored' => $request->is_sponsored ?? false,
                'sponsored_by' => $request->is_sponsored ? Auth::id() : null,
                'sponsored_until' => $request->sponsored_until,
                'featured_order' => $request->featured_order,
                'scheduled_publish_at' => $request->scheduled_publish_at,
                'rejection_reason' => $request->rejection_reason,
                'submitted_by' => $request->status == 'pending' ? Auth::id() : null,
                'submitted_at' => $request->status == 'pending' ? now() : null,
                'approved_by' => $request->status == 'approved' ? Auth::id() : null,
                'approved_at' => $request->status == 'approved' ? now() : null,
                'published_by' => $request->status == 'published' ? Auth::id() : null,
                'published_at' => $request->status == 'published' ? now() : null,
                'created_by' => Auth::id(),
                'seller_id' => $request->seller_id ?? null,
            ]);

            // Upload multiple images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $path = $img->store('pages/images', 'public');
                    PageImage::create(['page_id' => $page->id, 'image' => $path]);
                }
            }

            DB::commit();
            return redirect()->route('pages.index')->with('success', 'Page created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $page = DynamicPage::findOrFail($id);
        $categories = MarketplaceCategory::where('status', 1)->get();
        $sellers = Seller::all();
        $products = Product::where('status', 1)->get();
        return view('admin.pages.edit', compact('page', 'categories', 'sellers', 'products'));
    }

    public function update(Request $request, $id)
    {
        $page = DynamicPage::findOrFail($id);
        // Similar validation as store, add 'unique:dynamic_pages,slug,'.$id for slug
        // Then update all fields similarly
        // (Code similar to store but using $page->update())
    }

    public function destroy($id)
    {
        $page = DynamicPage::findOrFail($id);
        $page->delete();
        return back()->with('success', 'Page deleted.');
    }

    // Approval workflow actions (add these methods)
    public function submitForReview($id)
    {
        $page = DynamicPage::findOrFail($id);
        $page->update(['status' => 'pending', 'submitted_by' => Auth::id(), 'submitted_at' => now()]);
        return back()->with('success', 'Page submitted for review.');
    }

    public function approvePage($id)
    {
        $page = DynamicPage::findOrFail($id);
        $page->update(['status' => 'approved', 'approved_by' => Auth::id(), 'approved_at' => now()]);
        return back()->with('success', 'Page approved.');
    }

    public function rejectPage(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);
        $page = DynamicPage::findOrFail($id);
        $page->update(['status' => 'rejected', 'rejected_by' => Auth::id(), 'rejected_at' => now(), 'rejection_reason' => $request->reason]);
        return back()->with('success', 'Page rejected.');
    }

    public function publishPage($id)
    {
        $page = DynamicPage::findOrFail($id);
        $page->update(['status' => 'published', 'published_by' => Auth::id(), 'published_at' => now(), 'scheduled_publish_at' => null]);
        return back()->with('success', 'Page published.');
    }
    
    
    
    public function deleteImage($id)
{
    $image = PageImage::findOrFail($id);
    $image->delete();
    return response()->json(['success' => true]);
}
}
