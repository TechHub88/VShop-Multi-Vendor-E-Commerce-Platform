<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceSubCategory;
use App\Models\SellerBanner;
use App\Models\SellerOperationArea;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerSeo;
use App\Models\DynamicPage;
use App\Models\PromotionCampaign;
use App\Models\Advertisement;
use App\Models\WebsiteTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // ADD THIS LINE

use App\Models\RecommendationRule;
class AdminMarketplaceController extends Controller
{
   
   
     public function index(Request $request)
     {
         // Get all sellers with their data counts
         $sellers = Seller::withCount(['products', 'seoData', 'operationAreas', 'banners'])
             ->orderBy('name')
             ->get();
         
         return view('admin.marketplace-control', compact('sellers'));
     }
    // ==================== 1. CATEGORY MANAGEMENT ====================

    public function createCategory(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:marketplace_categories',
                'description' => 'nullable|string'
            ]);
            
            $apps = [];
            if($request->assign_to == 'vshop') {
                $apps = ['vShop'];
            } elseif($request->assign_to == 'vtravel') {
                $apps = ['vTravel'];
            } elseif($request->assign_to == 'both') {
                $apps = ['vShop', 'vTravel'];
            }
            
            $category = MarketplaceCategory::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'app_mapping' => json_encode($apps),
                'status' => 'active'
            ]);
            
            return response()->json(['success' => true, 'message' => 'Category created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function updateCategory(Request $request, $id)
    {
        try {
            $category = MarketplaceCategory::findOrFail($id);
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function deleteCategory($id)
    {
        try {
            $category = MarketplaceCategory::findOrFail($id);
            
            // Check if category has products
            $productCount = Product::where('marketplace_category_id', $id)->count();
            if($productCount > 0) {
                return response()->json(['success' => false, 'message' => "Category has {$productCount} products. Cannot delete."]);
            }
            
            // Delete subcategories first
            MarketplaceSubCategory::where('category_id', $id)->delete();
            $category->delete();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function toggleCategoryStatus($id)
    {
        try {
            $category = MarketplaceCategory::findOrFail($id);
            $category->status = $category->status == 'active' ? 'inactive' : 'active';
            $category->save();
            
            // If category deactivated, hide all products
            if($category->status == 'inactive') {
                Product::where('marketplace_category_id', $id)->update(['status' => 'hidden']);
            } else {
                Product::where('marketplace_category_id', $id)->update(['status' => 'active']);
            }
            
            return response()->json(['success' => true, 'status' => $category->status]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
    
    public function addSubcategory(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:marketplace_categories,id',
                'name' => 'required|string|max:255'
            ]);
            
            $subcategory = MarketplaceSubCategory::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'status' => 'active'
            ]);
            
            return response()->json(['success' => true, 'subcategory' => $subcategory]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function deleteSubcategory($id)
    {
        try {
            MarketplaceSubCategory::findOrFail($id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
    
    public function mapCategoryToApp(Request $request)
    {
        try {
            $category = MarketplaceCategory::findOrFail($request->category_id);
            $category->app_mapping = json_encode($request->apps);
            $category->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    // ==================== 2. BANNER MANAGEMENT ====================
    
    public function uploadMultipleBanners(Request $request)
    {
        try {
            $request->validate([
                'banners.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
            ]);
            
            $uploaded = [];
            if($request->hasFile('banners')) {
                foreach($request->file('banners') as $image) {
                    $path = $image->store('banners', 'public');
                    $banner = SellerBanner::create([
                        'type' => 'multiple',
                        'image' => $path,
                        'status' => 'approved'
                    ]);
                    $uploaded[] = $banner;
                }
            }
            return response()->json(['success' => true, 'count' => count($uploaded)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function uploadSingleBanner(Request $request)
    {
        try {
            $request->validate([
                'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
            ]);
            
            SellerBanner::where('type', 'single')->delete();
            
            $path = $request->file('banner_image')->store('banners', 'public');
            $banner = SellerBanner::create([
                'type' => 'single',
                'image' => $path,
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'link' => $request->link,
                'status' => 'approved'
            ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function uploadStaticBanner(Request $request)
    {
        try {
            $request->validate([
                'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
            ]);
            
            $path = $request->file('banner_image')->store('banners', 'public');
            $banner = SellerBanner::create([
                'type' => 'static',
                'image' => $path,
                'position' => $request->position ?? 'sidebar',
                'status' => 'approved'
            ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function uploadDynamicBanner(Request $request)
    {
        try {
            $request->validate([
                'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
                'dynamic_type' => 'required'
            ]);
            
            $path = $request->file('banner_image')->store('banners', 'public');
            $banner = SellerBanner::create([
                'type' => 'dynamic',
                'image' => $path,
                'dynamic_type' => $request->dynamic_type,
                'dynamic_value' => $request->dynamic_value,
                'status' => 'approved'
            ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function approveBanner($id)
    {
        try {
            SellerBanner::findOrFail($id)->update(['status' => 'approved']);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
    
    public function deleteBanner($id)
    {
        try {
            $banner = SellerBanner::findOrFail($id);
            if($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $banner->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    // ==================== 3. BOOST PRIORITY ENGINE ====================
    
    public function toggleFeatured($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->is_featured = !$product->is_featured;
            $product->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
    
    public function toggleSponsored($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->is_sponsored = !$product->is_sponsored;
            $product->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
    
    public function addFeaturedProduct(Request $request)
    {
        try {
            $product = Product::findOrFail($request->product_id);
            $product->is_featured = true;
            $product->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ==================== 4. WEBSITE TEMPLATES ====================
    
    public function uploadTemplate(Request $request)
    {
        try {
            $request->validate([
                'template_name' => 'required|string',
                'template_file' => 'required|file|mimes:zip,jpg,png|max:10240',
                'app_type' => 'required|string'
            ]);
            
            $path = $request->file('template_file')->store('templates', 'public');
            WebsiteTemplate::create([
                'name' => $request->template_name,
                'file_path' => $path,
                'app_type' => $request->app_type,
                'is_active' => true
            ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function assignTemplate(Request $request)
    {
        try {
            $template = $request->template_name;
            Seller::where('status', 'approved')->update(['template_used' => $template]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    // ==================== 5. SEO MONITORING ====================
    
    public function blockSpamKeyword(Request $request)
    {
        try {
            $keyword = $request->keyword;
            $blockedKeywords = cache('blocked_keywords', []);
            if(!in_array($keyword, $blockedKeywords)) {
                $blockedKeywords[] = $keyword;
                cache(['blocked_keywords' => $blockedKeywords], 86400);
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
    
    public function suggestSEO($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            $suggestion = [
                'title' => 'Best ' . $product->name . ' - Buy Online',
                'description' => 'Shop for ' . $product->name . ' at best prices.'
            ];
            return response()->json(['success' => true, 'suggestion' => $suggestion]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
    
    public function runSEOAudit()
    {
        try {
            $products = Product::select('id', 'name', 'seo_title', 'seo_description')->get();
            $issues = [];
            foreach($products as $product) {
                if(empty($product->seo_title)) {
                    $issues[] = ['product_id' => $product->id, 'name' => $product->name, 'issue' => 'Missing SEO Title'];
                }
            }
            return response()->json(['success' => true, 'issues' => $issues, 'total' => $products->count()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    // ==================== 6. OPERATION AREAS ====================
    
    public function validateArea($id)
    {
        try {
            $area = SellerOperationArea::findOrFail($id);
            $area->is_validated = true;
            $area->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
    
    public function invalidateArea($id)
    {
        try {
            $area = SellerOperationArea::findOrFail($id);
            $area->is_validated = false;
            $area->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    // ==================== 7. SEARCH & DISCOVERY ====================
    
    public function updateRankingRules(Request $request)
    {
        try {
            cache(['search_ranking_rules' => $request->all()], 86400);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    // ==================== 8. DYNAMIC PAGE MANAGEMENT ====================
    
    public function createDynamicPage(Request $request)
    {
        try {
            $page = DynamicPage::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'content' => $request->content,
                'status' => 'draft',
                'app_type' => $request->app_type ?? 'all'
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function approveDynamicPage($id)
    {
        try {
            DynamicPage::findOrFail($id)->update(['status' => 'approved']);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
    
    public function publishDynamicPage($id)
    {
        try {
            DynamicPage::findOrFail($id)->update(['status' => 'published']);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
    
    public function deleteDynamicPage($id)
    {
        try {
            DynamicPage::findOrFail($id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    // ==================== 9. SMART SUGGESTIONS ====================
    
    public function getSmartSuggestions()
    {
        try {
            $suggestions = [
                'app_wise' => [
                    'vshop' => ['Top 10 Electronics this week'],
                    'vtravel' => ['Top Destinations for Weekend']
                ]
            ];
            return response()->json(['success' => true, 'suggestions' => $suggestions]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    // ==================== 10. PROMOTION CAMPAIGNS ====================
    
    public function createCampaign(Request $request)
    {
        try {
            $campaign = PromotionCampaign::create([
                'name' => $request->name,
                'description' => $request->description,
                'discount' => $request->discount,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'active'
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function endCampaign($id)
    {
        try {
            PromotionCampaign::findOrFail($id)->update(['status' => 'ended']);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    // ==================== 11. ADVERTISEMENTS ====================
    
    public function createAdvertisement(Request $request)
    {
        try {
            $path = $request->file('image')->store('ads', 'public');
            Advertisement::create([
                'title' => $request->title,
                'image' => $path,
                'position' => $request->position,
                'link' => $request->link,
                'status' => 'active'
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ==================== VERIFICATION & APPROVAL FLOW ====================

    public function verify($id)
    {
        $seller = Seller::findOrFail($id);
        
        // Get seller-specific categories
        $categories = MarketplaceCategory::where('seller_id', $seller->id)
            ->with('subcategories')
            ->orderBy('name')
            ->get();
        
        // Banner data
        $multipleBanners = SellerBanner::where('seller_id', $seller->id)
            ->where('type', 'multiple')
            ->orderBy('created_at', 'desc')
            ->get();
            
            
                $banners = SellerBanner::where('seller_id', $seller->id)->get();
    
  
            
        $staticBanners = SellerBanner::where('seller_id', $seller->id)
            ->where('type', 'static')
            ->get();
        
        // Operation areas
        $operationAreas = SellerOperationArea::where('seller_id', $seller->id)
            ->with(['country', 'state', 'city'])
            ->latest()
            ->get();
        
        // SEO data
        $sellerProducts = SellerSeo::where('seller_id', $seller->id)
            ->latest()
            ->get();
        
        // Templates
        $templates = WebsiteTemplate::orderBy('created_at', 'desc')->get();
        $assignedTemplates = \App\Models\SellerWebsiteTemplate::where('seller_id', $seller->id)
            ->with('template')
            ->get();
        
        // Additional stats
        $totalProducts = Product::where('created_by_id', $seller->id)->count();
        $totalOrders = \App\Models\Order::where('seller_id', $seller->id)->count();
        
        // Products list (Products & Boost tab)
        $products = Product::where(function($q) use ($seller) {
                $q->where('created_by_id', $seller->id)
                  ->orWhere('created_by_id', $seller->id);
            })->latest()->take(60)->get();

        // Boost/Featured products
        $boostProducts = Product::where(function($q) use ($seller) {
                $q->where('created_by_id', $seller->id)
                  ->orWhere('created_by_id', $seller->id);
            })->where(function($q2) {
                $q2->where('is_featured', 1)->orWhere('boost_status', 1)->orWhere('is_sponsored', 1);
            })->latest()->get();

        // Dynamic pages
        $dynamicPages = DynamicPage::where('seller_id', $seller->id)->latest()->get();

        // Campaigns
        $campaigns = PromotionCampaign::where('seller_id', $seller->id)->latest()->get();

        // Advertisements
        $advertisements = Advertisement::where('seller_id', $seller->id)->latest()->get();
        
        return view('admin.marketplace-verify', compact(
            'seller',
            'categories', 
            'multipleBanners', 
            'banners', 
            'staticBanners',
            'operationAreas', 
            'sellerProducts', 
            'templates',
            'assignedTemplates',
            'totalProducts',
            'totalOrders',
            'products',
            'boostProducts',
            'dynamicPages',
            'campaigns',
            'advertisements'
        ));
    }

   
public function searchDiscovery(Request $request)
    {
        $featuredProducts = Product::where('is_featured', true)
            ->with('seller')
            ->latest()
            ->get();
            
        $sponsoredProducts = Product::where('is_sponsored', '1')
            ->with('seller')
            ->latest()
            ->get();

        $pinnedProducts = Product::where('is_pinned', '1')
            ->with('seller')
            ->latest()
            ->get();

        // Get all boosted products (combine all)
        $boostedProducts = Product::where(function($query) {
                $query->where('is_featured', true)
                      ->orWhere('is_sponsored', '1')
                      ->orWhere('is_pinned', '1');
            })
            ->with('seller')
            ->latest()
            ->get();

        $allProducts = Product::with('seller')->latest()->take(100)->get();

        return view('admin.marketplace-search', compact(
            'featuredProducts',
            'sponsoredProducts',
            'pinnedProducts',
            'boostedProducts',
            'allProducts'
        ));
    }

    // ADD THIS METHOD - Add Multiple Boosts
    public function addMultipleBoosts(Request $request)
    {
        try {
            Log::info('Add Multiple Boosts called', $request->all());
            
            $productIds = $request->product_ids;
            $boostType = $request->boost_type;
            
            if(empty($productIds)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No products selected'
                ]);
            }
            
            if(empty($boostType)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Please select boost type'
                ]);
            }
            
            $updatedCount = 0;
            
            foreach($productIds as $productId) {
                $product = Product::find($productId);
                if($product) {
                    switch($boostType) {
                        case 'featured':
                            $product->is_featured = true;
                            break;
                        case 'sponsored':
                            $product->is_sponsored = '1';
                            break;
                        case 'pinned':
                            $product->is_pinned = '1';
                            break;
                    }
                    $product->save();
                    $updatedCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => $updatedCount . ' product(s) boosted successfully'
            ]);
            
        } catch(\Exception $e) {
            Log::error('Add Multiple Boosts Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ADD THIS METHOD - Remove Boost
    public function removeBoost(Request $request, $id)
    {
        try {
            $product = Product::find($id);
            
            if(!$product) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Product not found'
                ]);
            }
            
            $type = $request->type;
            
            if($type == 'all') {
                $product->is_featured = false;
                $product->is_sponsored = '0';
                $product->is_pinned = '0';
            } else {
                switch($type) {
                    case 'featured':
                        $product->is_featured = false;
                        break;
                    case 'sponsored':
                        $product->is_sponsored = '0';
                        break;
                    case 'pinned':
                        $product->is_pinned = '0';
                        break;
                }
            }
            
            $product->save();
            
            return response()->json([
                'success' => true, 
                'message' => 'Boost removed successfully'
            ]);
            
        } catch(\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    // ADD THIS METHOD - Get Boost (for Edit Modal)
    public function getBoost($id)
    {
        try {
            $product = Product::find($id);
            
            if(!$product) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Product not found'
                ]);
            }
            
            return response()->json([
                'is_featured' => $product->is_featured ? true : false,
                'is_sponsored' => $product->is_sponsored == '1' ? true : false,
                'is_pinned' => $product->is_pinned ? true : false
            ]);
            
        } catch(\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    // ADD THIS METHOD - Update Boost
    public function updateBoost(Request $request, $id)
    {
        try {
            $product = Product::find($id);
            
            if(!$product) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Product not found'
                ]);
            }
            
            $product->is_featured = $request->is_featured ? true : false;
            $product->is_sponsored = $request->is_sponsored ? '1' : '0';
            $product->is_pinned = $request->is_pinned ? true : false;
            $product->save();
            
            return response()->json([
                'success' => true, 
                'message' => 'Boost settings updated successfully'
            ]);
            
        } catch(\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    // ADD THIS METHOD - Toggle Product Status
    public function toggleProductStatus(Request $request, $id)
    {
        try {
            $product = Product::find($id);
            
            if(!$product) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Product not found'
                ]);
            }
            
            $product->status = $request->status;
            $product->save();
            
            return response()->json([
                'success' => true, 
                'message' => 'Product status updated successfully'
            ]);
            
        } catch(\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    // ADD THIS METHOD - Export Boosted Products
    public function exportBoostedProducts()
    {
        try {
            $products = Product::where(function($query) {
                $query->where('is_featured', true)
                      ->orWhere('is_sponsored', '1')
                      ->orWhere('is_pinned', '1');
            })
            ->with('seller')
            ->get();
            
            // Create CSV export
            $filename = 'boosted-products-' . date('Y-m-d') . '.csv';
            $handle = fopen('php://temp', 'w');
            
            // Add headers
            fputcsv($handle, ['ID', 'Product Name', 'SKU', 'Seller', 'Price', 'Boost Type', 'Status']);
            
            // Add data
            foreach($products as $product) {
                $boostTypes = [];
                if($product->is_pinned) $boostTypes[] = 'Pinned';
                if($product->is_featured) $boostTypes[] = 'Featured';
                if($product->is_sponsored) $boostTypes[] = 'Sponsored';
                
                fputcsv($handle, [
                    $product->id,
                    $product->name,
                    $product->sku ?? 'N/A',
                    $product->seller->name ?? 'Admin',
                    $product->price,
                    implode(', ', $boostTypes),
                    $product->status
                ]);
            }
            
            rewind($handle);
            $csv = stream_get_contents($handle);
            fclose($handle);
            
            return response($csv, 200)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
        } catch(\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }


    public function promotionsView(Request $request)
    {
        $activeCampaigns = PromotionCampaign::latest()->get();
        return view('admin.marketplace-promotions', compact('activeCampaigns'));
    }

    public function smartSuggestionsView(Request $request)
    {
        $totalProducts = Product::count();
        $totalOrders = \App\Models\Order::count();
        return view('admin.marketplace-suggestions', compact('totalProducts', 'totalOrders'));
    }

    public function approveEntity($entity, $id)
    {
        try {
            switch ($entity) {
                case 'category':
                    $item = MarketplaceCategory::findOrFail($id);
                    $item->status = 1;
                    $item->rejection_reason = null;
                    $item->save();
                    break;
                case 'banner':
                    $item = SellerBanner::findOrFail($id);
                    $item->status = 'approved';
                    $item->rejection_reason = null;
                    $item->save();
                    break;
                case 'product':
                    $item = Product::findOrFail($id);
                    $item->verification_status = 'approved';
                    $item->status = 'active';
                    $item->rejection_reason = null;
                    $item->save();
                    break;
                case 'product-boost':
                    $item = Product::findOrFail($id);
                    $item->boost_status = 1;
                    $item->save();
                    break;
                case 'area':
                    $item = SellerOperationArea::findOrFail($id);
                    $item->is_validated = 1;
                    $item->rejection_reason = null;
                    $item->save();
                    break;
                case 'page':
                    $item = DynamicPage::findOrFail($id);
                    $item->status = 'approved';
                    $item->rejection_reason = null;
                    $item->save();
                    break;
                case 'campaign':
                    $item = PromotionCampaign::findOrFail($id);
                    $item->status = 'active';
                    $item->rejection_reason = null;
                    $item->save();
                    break;
                case 'ad':
                    $item = Advertisement::findOrFail($id);
                    $item->status = 'active';
                    $item->rejection_reason = null;
                    $item->save();
                    break;
                case 'template':
                    $item = \App\Models\SellerWebsiteTemplate::findOrFail($id);
                    $item->status = 'approved';
                    $item->rejection_reason = null;
                    $item->save();
                    break;
                case 'seo':
                    $item = SellerSeo::findOrFail($id);
                    $item->status = 'approved';
                    $item->save();
                    break;
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid entity type']);
            }
            return response()->json(['success' => true, 'message' => ucfirst(str_replace('-', ' ', $entity)) . ' approved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function rejectEntity($entity, $id)
    {
        try {
            $reason = request('reason', '');

            switch ($entity) {
                case 'category':
                    $item = MarketplaceCategory::findOrFail($id);
                    $item->status = 0;
                    $item->rejection_reason = $reason;
                    $item->save();
                    break;
                case 'banner':
                    $item = SellerBanner::findOrFail($id);
                    $item->status = 'rejected';
                    $item->rejection_reason = $reason;
                    $item->save();
                    break;
                case 'product':
                    $item = Product::findOrFail($id);
                    $item->verification_status = 'rejected';
                    $item->status = 'inactive';
                    $item->rejection_reason = $reason;
                    $item->save();
                    break;
                case 'product-boost':
                    $item = Product::findOrFail($id);
                    $item->boost_status = 0;
                    $item->save();
                    break;
                case 'area':
                    $item = SellerOperationArea::findOrFail($id);
                    $item->is_validated = 0;
                    $item->rejection_reason = $reason;
                    $item->save();
                    break;
                case 'page':
                    $item = DynamicPage::findOrFail($id);
                    $item->status = 'rejected';
                    $item->rejection_reason = $reason;
                    $item->save();
                    break;
                case 'campaign':
                    $item = PromotionCampaign::findOrFail($id);
                    $item->status = 'inactive';
                    $item->rejection_reason = $reason;
                    $item->save();
                    break;
                case 'ad':
                    $item = Advertisement::findOrFail($id);
                    $item->status = 'inactive';
                    $item->rejection_reason = $reason;
                    $item->save();
                    break;
                case 'template':
                    $item = \App\Models\SellerWebsiteTemplate::findOrFail($id);
                    $item->status = 'rejected';
                    $item->rejection_reason = $reason;
                    $item->save();
                    break;
                case 'seo':
                    $item = SellerSeo::findOrFail($id);
                    $item->status = 'rejected';
                    $item->rejection_reason = $reason;
                    $item->save();
                    break;
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid entity type']);
            }
            return response()->json(['success' => true, 'message' => ucfirst(str_replace('-', ' ', $entity)) . ' rejected' . ($reason ? ' — Reason: ' . $reason : '') . '.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ==================== GENERAL ====================
    
    public function exportMarketplaceData(Request $request)
    {
        $type = $request->type;
        $data = MarketplaceCategory::all();
        $filename = $type . '_export_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['ID', 'Name', 'Status', 'Created At']);
        
        foreach($data as $row) {
            fputcsv($handle, [$row->id, $row->name, $row->status, $row->created_at]);
        }
        
        fclose($handle);
        exit;
    }
    
    
    public function getGlobalRules()
{
    $rules = GlobalRule::latest()->get();

    return response()->json([
        'status' => true,
        'data'   => $rules
    ]);
}




//recomendation


public function recommendationsIndex(Request $request)
{
    // Get all active product-type recommendation rules (source is a product ID)
    $rules = RecommendationRule::where('type', 'product')
                ->where('is_active', true)
                ->get();

    // Build an array: [source_product_id => [target_product_ids]]
    $sourceToTargets = [];
    foreach ($rules as $rule) {
        $sourceId = $rule->source; // assuming source stores product_id
        $targets = is_array($rule->targets) ? $rule->targets : json_decode($rule->targets, true);
        if (!empty($targets)) {
            if (!isset($sourceToTargets[$sourceId])) {
                $sourceToTargets[$sourceId] = [];
            }
            $sourceToTargets[$sourceId] = array_merge($sourceToTargets[$sourceId], $targets);
        }
    }

    // Get unique source product IDs
    $sourceProductIds = array_keys($sourceToTargets);
    if (empty($sourceProductIds)) {
        $productsWithRecs = [];
    } else {
        // Load source products with their category
        $sourceProducts = Product::whereIn('id', $sourceProductIds)
            ->with('category')
            ->get()
            ->keyBy('id');

        // Collect all recommended product IDs
        $allRecIds = [];
        foreach ($sourceToTargets as $targetIds) {
            $allRecIds = array_merge($allRecIds, $targetIds);
        }
        $allRecIds = array_unique($allRecIds);

        // Load recommended products (only needed fields)
        $recProducts = Product::whereIn('id', $allRecIds)
            ->select('id', 'name')
            ->get()
            ->keyBy('id');

        // Assemble final array
        $productsWithRecs = [];
        foreach ($sourceToTargets as $sourceId => $targetIds) {
            $product = $sourceProducts->get($sourceId);
            if (!$product) continue;

            $recommendedProducts = [];
            foreach ($targetIds as $recId) {
                if ($recProducts->has($recId)) {
                    $recommendedProducts[] = $recProducts->get($recId);
                }
            }

            $productsWithRecs[] = [
                'product' => $product,
                'category_name' => $product->category ? $product->category->name : null,
                'recommended_products' => $recommendedProducts,
            ];
        }
    }

    // Return the view with the data
    return view('admin.marketplace-recommendations', compact('productsWithRecs'));
}
public function recommendationStore(Request $request)
{
    $request->validate([
        'type' => 'required|in:product,location,ai,sponsored',
        'source' => 'required|string|max:255',
        'source_type' => 'nullable|string',
        'targets' => 'required|array|min:1',
        'priority' => 'integer|min:0'
    ]);

    RecommendationRule::create([
        'type' => $request->type,
        'source' => $request->source,
        'source_type' => $request->source_type,
        'targets' => json_encode($request->targets),
        'priority' => $request->priority ?? 0,
        'is_active' => true
    ]);

    return response()->json(['success' => true, 'message' => 'Rule created successfully.']);
}

public function recommendationEdit($id)
{
    $rule = RecommendationRule::findOrFail($id);
    return response()->json($rule);
}

public function recommendationUpdate(Request $request, $id)
{
    $rule = RecommendationRule::findOrFail($id);

    $request->validate([
        'type' => 'required|in:product,location,ai,sponsored',
        'source' => 'required|string|max:255',
        'source_type' => 'nullable|string',
        'targets' => 'required|array|min:1',
        'priority' => 'integer|min:0'
    ]);

    $rule->update([
        'type' => $request->type,
        'source' => $request->source,
        'source_type' => $request->source_type,
        'targets' => json_encode($request->targets),
        'priority' => $request->priority ?? 0
    ]);

    return response()->json(['success' => true, 'message' => 'Rule updated successfully.']);
}

public function recommendationDestroy($id)
{
    RecommendationRule::findOrFail($id)->delete();
    return response()->json(['success' => true, 'message' => 'Rule deleted.']);
}

public function recommendationToggleStatus($id)
{
    $rule = RecommendationRule::findOrFail($id);
    $rule->is_active = !$rule->is_active;
    $rule->save();
    return response()->json(['success' => true, 'status' => $rule->is_active]);
}
}