<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerSeo;
use App\Models\WebsiteTemplate;
use App\Models\SellerWebsiteTemplate;
use App\Models\SellerOperationArea;
use App\Models\SellerBanner;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\DB;
use App\Models\MarketplaceCategory;
use App\Models\SellerMarketplaceCategory;
use App\Models\MarketplaceSubCategory;
use App\Services\ImageModeratorService;

class MarketplaceController extends Controller
{

/* =====================================================
   STEP PROGRESS CHECK
===================================================== */




/* =====================================================
   VERIFICATION STATUS
===================================================== */

private function verificationStatus()
{
    $sellerId = Auth::guard('seller')->id();

    return [

        'category' => SellerMarketplaceCategory::where('seller_id',$sellerId)->exists()
            ? 'verified'
            : 'pending',

        'banner' => SellerBanner::where('seller_id',$sellerId)->exists()
            ? 'verified'
            : 'pending',

        'template' => SellerWebsiteTemplate::where('seller_id',$sellerId)->exists()
            ? 'verified'
            : 'pending',

        'seo' => SellerSeo::where('seller_id',$sellerId)->exists()
            ? 'verified'
            : 'pending',

        'area' => SellerOperationArea::where('seller_id',$sellerId)->exists()
            ? 'verified'
            : 'pending',
    ];
}


private function marketplaceProgress()
{
    $sellerId = Auth::guard('seller')->id();
    return [
        'category' => SellerMarketplaceCategory::where('seller_id',$sellerId)->exists(),
        'banner' => SellerBanner::where('seller_id',$sellerId)->exists(),
        'template' => SellerWebsiteTemplate::where('seller_id',$sellerId)->exists(),
        'seo' => SellerSeo::where('seller_id',$sellerId)->exists(),
        'area' => SellerOperationArea::where('seller_id',$sellerId)->exists(),
    ];
}

/* =====================================================
   CURRENT ACTIVE STEP
===================================================== */

private function currentStep($progress)
{
    if(!$progress['category']) return 'category';
    if(!$progress['banner']) return 'banner';
    if(!$progress['template']) return 'template';
    if(!$progress['seo']) return 'seo';
    if(!$progress['area']) return 'area';

    // âœ… ALL STEPS COMPLETE
    return null;
}


private function notify($type, $message, $route = null)
{
    try {

        $sellerId = auth('seller')->id();

        \App\Models\Notification::create([
            'seller_id' => $sellerId,
            'type' => $type,
            'message' => $message,
            'link' => $route ? route($route) : null,
        ]);

    } catch (\Exception $e) {
        // optional: log error
        \Log::error('Notification Error: '.$e->getMessage());
    }
}

/* =====================================================
   UNIFIED SETUP WIZARD (SPA)
===================================================== */
public function setup()
{
    $sellerId = Auth::guard('seller')->id();

    // 1. Progress
    $progress = $this->marketplaceProgress();
    $currentStep = $this->currentStep($progress);
    $verificationStatus = $this->verificationStatus();

    // 2. Category Data
    $categories = MarketplaceCategory::with(['subCategories' => function ($q) {
        $q->where('status', 1);
    }])->where('status', 1)->where('seller_id', $sellerId)
       ->whereIn('type', ['product', 'service'])->orderBy('name', 'ASC')->get();
    $selectedCategories = SellerMarketplaceCategory::where('seller_id', $sellerId)->pluck('sub_category_id')->toArray();

    // 3. Banner Data
    $banner = SellerBanner::where('seller_id', $sellerId)->first();

    // 4. Template Data
    $templates = WebsiteTemplate::where(function($q) use ($sellerId){
        $q->whereNull('seller_id')->orWhere('seller_id',$sellerId);
    })->get();
    $selectedTemplates = \DB::table('seller_website_templates')->where('seller_id', $sellerId)->pluck('template_id')->toArray();

    // 5. SEO Data
    $seo = SellerSeo::where('seller_id', $sellerId)->first();

    // 6. Area Data
    $countries = Country::select('id','name')->orderBy('name')->get();

    return view('vshop.seller.marketplace.setup', compact(
        'progress', 'currentStep', 'verificationStatus',
        'categories', 'selectedCategories',
        'banner',
        'templates', 'selectedTemplates',
        'seo',
        'countries'
    ));
}

/* =====================================================
   CATEGORY
===================================================== */

public function category()
{
    $sellerId = Auth::guard('seller')->id();

    // --- अब moduleType की ज़रूरत नहीं ---
    // $moduleType = $this->getModuleType();

    $query = MarketplaceCategory::with([
        'subCategories' => function ($q) {
            $q->where('status', 1);
        }
    ])
    ->where('status', 1)
    ->where('seller_id', $sellerId)
    ->whereIn('type', ['product', 'service']);   // सिर्फ product और service

    // --- सभी कैटेगरी लें ---
    $categories = $query->orderBy('name', 'ASC')->get();

    // selected sub categories (यह वैसा ही रहेगा)
    $selected = SellerMarketplaceCategory::where('seller_id', $sellerId)
                ->pluck('sub_category_id')
                ->toArray();

    // progress, currentStep, verificationStatus (जैसे हैं वैसे रखें)
    $progress = $this->marketplaceProgress();
    $currentStep = $this->currentStep($progress);
    $verificationStatus = $this->verificationStatus();

    return view('vshop.seller.marketplace.category', compact(
        'categories', 'selected', 'progress', 'currentStep', 'verificationStatus'
    ));
}

public function saveCategory(Request $request)
{
    $seller = Auth::guard('seller')->user();

    if (!$seller) {
        return redirect()->route('seller.login');
    }

    $sellerId = $seller->id;

    // âœ… Validate
    $request->validate([
        'sub_categories' => 'required|array',
        'sub_categories.*' => 'exists:marketplace_sub_categories,id'
    ]);

    // âœ… Delete old data for THIS seller only
    SellerMarketplaceCategory::where('seller_id', $sellerId)->delete();

    $insertData = [];

    foreach ($request->sub_categories as $sub) {

        $subCategory = MarketplaceSubCategory::find($sub);

        if ($subCategory) {

            $insertData[] = [
                'seller_id'       => $sellerId,
                'category_id'     => $subCategory->category_id,
                'sub_category_id' => $subCategory->id,
                'created_at'      => now(),
                'updated_at'      => now()
            ];
        }
    }

    // âœ… Insert only if data exists
    if (!empty($insertData)) {
        SellerMarketplaceCategory::insert($insertData);

        // ðŸ”” NOTIFICATION
        $this->notify(
            'marketplace',
            count($insertData) . ' sub-categories selected for your marketplace',
            'seller.marketplace.category.list'
        );
    }

    if ($request->ajax()) {
        return response()->json(['status' => true, 'message' => 'Category Saved Successfully']);
    }

    return redirect()
        ->route('seller.marketplace.category.list')
        ->with('success', 'Category Saved Successfully');
}


public function categoryList()
{
    $sellerId = Auth::guard('seller')->id();

    /*
    |--------------------------------------------------------------------------
    | SELECTED CATEGORY + SUBCATEGORY
    |--------------------------------------------------------------------------
    */

    $selectedCategories = SellerMarketplaceCategory::join(
            'marketplace_categories',
            'marketplace_categories.id',
            '=',
            'seller_marketplace_categories.category_id'
        )
        ->leftJoin(
            'marketplace_sub_categories',
            'marketplace_sub_categories.id',
            '=',
            'seller_marketplace_categories.sub_category_id'
        )
        ->selectRaw('
            marketplace_categories.id,
            marketplace_categories.name,

            GROUP_CONCAT(
                DISTINCT marketplace_sub_categories.name
                ORDER BY marketplace_sub_categories.name ASC
                SEPARATOR ", "
            ) as subs
        ')
        ->where(
            'seller_marketplace_categories.seller_id',
            $sellerId
        )
        ->groupBy(
            'marketplace_categories.id',
            'marketplace_categories.name'
        )
        ->orderBy(
            'marketplace_categories.name',
            'ASC'
        )
        ->get();


    /*
    |--------------------------------------------------------------------------
    | NOT SELECTED CATEGORY
    |--------------------------------------------------------------------------
    */

    $selectedIds = $selectedCategories->pluck('id')->toArray();

    $notSelected = MarketplaceCategory::whereNotIn(
            'id',
            $selectedIds
        )
        ->where('status',1)
        ->orderBy('name','ASC')
        ->pluck('name');


    /*
    |--------------------------------------------------------------------------
    | PROGRESS + STATUS
    |--------------------------------------------------------------------------
    */

    $progress = $this->marketplaceProgress();

    $currentStep = $this->currentStep($progress);

    $verificationStatus = $this->verificationStatus();


    /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

    return view(
        'vshop.seller.marketplace.category-list',
        compact(
            'selectedCategories',
            'notSelected',
            'progress',
            'currentStep',
            'verificationStatus'
        )
    );
}

public function deleteCategory(Request $request)
{
    $sellerId = Auth::guard('seller')->id();
    $categoryId = $request->category_id;

    // delete all subcategories under this category for seller
    SellerMarketplaceCategory::where('seller_id', $sellerId)
        ->where('category_id', $categoryId)
        ->delete();

    // NOTIFICATION
    $this->notify(
        'marketplace',
        'Category removed from your marketplace',
        'seller.marketplace.category.list'
    );

    return back()->with('success', 'Category deleted successfully');
}

public function destroyCategory(Request $request)
{
    $sellerId = Auth::guard('seller')->id();
    $categoryId = $request->category_id;

    if(!$categoryId){
        return response()->json(['status'=>false,'msg'=>'ID missing']);
    }

    // Get category name for notification
    $category = MarketplaceCategory::find($categoryId);
    $categoryName = $category ? $category->name : 'Category';

    // delete subcategories
    MarketplaceSubCategory::where('category_id', $categoryId)->delete();

 
    $deleted = MarketplaceCategory::where('id', $categoryId)->delete();

    if($deleted){
        // NOTIFICATION
        $this->notify(
            'marketplace',
            $categoryName . ' deleted successfully',
            'seller.marketplace.category'
        );
        return response()->json(['status'=>true]);
    }else{
        return response()->json(['status'=>false,'msg'=>'Delete failed']);
    }
}

public function banner($id = null)
{
    $progress = $this->marketplaceProgress();
    $currentStep = $this->currentStep($progress);

    if($currentStep!='banner' && !$progress['banner']){
        return redirect()->route('seller.marketplace.'.$currentStep);
    }

    $sellerId = Auth::guard('seller')->id();

    // banner list
    $banners = SellerBanner::where('seller_id',$sellerId)
                ->latest()
                ->get();

    // edit banner
    $banner = null;

    if($id){
        $banner = SellerBanner::where('seller_id',$sellerId)
                    ->findOrFail($id);
    }
    $verificationStatus = $this->verificationStatus();
    return view(
        'vshop.seller.marketplace.banner',
        compact(
            'banners',
            'banner',
            'progress',
            'currentStep','verificationStatus'
        )
    );
}


public function bannerUpdate(Request $request, $id)
{
    $sellerId = Auth::guard('seller')->id();

    $banner = SellerBanner::where('seller_id', $sellerId)
        ->findOrFail($id);

    // ================= VALIDATION =================
    $request->validate([
        'banner_title'      => 'required|max:100',
        'banner_sub_title'  => 'nullable|max:150',
        'banner_image.*'    => 'image|mimes:jpg,jpeg,png|max:2048'
    ]);

    // ================= NSFW CHECK =================
    if ($request->hasFile('banner_image')) {
        foreach ($request->file('banner_image') as $file) {
            if (!ImageModeratorService::isSafeImage($file->getRealPath())) {
                return back()->withErrors(['banner_image' => 'The uploaded image contains inappropriate (NSFW) content.'])->withInput();
            }
        }
    }

    // ================= OLD IMAGES =================
    $oldImages = $banner->banner_image ?? [];

    if (!is_array($oldImages)) {
        $oldImages = json_decode($oldImages, true) ?? [];
    }

    // ================= REMOVE DELETED =================
    if ($request->filled('existing_images')) {
        $oldImages = json_decode($request->existing_images, true);
    }

    // ================= UPLOAD NEW =================
    $folder = 'seller-banners/' . $sellerId;

    if (!file_exists(public_path($folder))) {
        mkdir(public_path($folder), 0777, true);
    }

    if ($request->hasFile('banner_image')) {

        foreach ($request->file('banner_image') as $index => $file) {

            $filename = time().'_'.$index.'_'.uniqid().'.'.$file->getClientOriginalExtension();

            $file->move(public_path($folder), $filename);

            $oldImages[] = $folder.'/'.$filename;
        }
    }

    // ================= UPDATE =================
    $banner->update([
        'banner_title'     => $request->banner_title,
        'banner_sub_title' => $request->banner_sub_title,
        'banner_image'     => $oldImages, // âœ… ARRAY SAVE
        'status'           => 'pending'
    ]);

    // ================= NOTIFY =================
    $this->notify(
        'banner',
        'Banner "' . $banner->banner_title . '" Updated Successfully',
        'seller.marketplace.banner.list'
    );

    if ($request->ajax()) {
        return response()->json(['status' => true, 'message' => 'Banner Updated Successfully']);
    }

    return redirect()
        ->route('seller.marketplace.banner.list')
        ->with('success', 'Banner Updated Successfully');
}


public function bannerDelete($id)
{
    $sellerId = Auth::guard('seller')->id();

    $banner = SellerBanner::where('seller_id', $sellerId)
        ->findOrFail($id);

    $bannerTitle = $banner->banner_title;

    // ✅ FIX: ensure image is string
    $image = $banner->banner_image;

    if (is_array($image)) {
        $image = $image[0] ?? null;
    }

    if (!empty($image)) {

        $filePath = public_path($image);

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $banner->delete();

    // notify
    $this->notify(
        'banner',
        'Banner "' . $bannerTitle . '" Deleted Successfully',
        'seller.marketplace.banner.list'
    );

    return redirect()
        ->route('seller.marketplace.banner.list')
        ->with('success', 'Banner Deleted Successfully');
}

public function bannerStore(Request $request)
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        return redirect()->route('seller.login');
    }

    // ================= VALIDATION =================
    $request->validate([
        'banner_title'      => 'required|string|max:100',
        'banner_sub_title'  => 'nullable|string|max:150',

        // 🔥 MULTIPLE IMAGE REQUIRED
        'banner_image'      => 'required|array|min:1',
        'banner_image.*'    => 'image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // ================= NSFW CHECK =================
    if ($request->hasFile('banner_image')) {
        foreach ($request->file('banner_image') as $file) {
            if (!ImageModeratorService::isSafeImage($file->getRealPath())) {
                return back()->withErrors(['banner_image' => 'The uploaded image contains inappropriate (NSFW) content.'])->withInput();
            }
        }
    }

    // ================= FOLDER =================
    $folder = 'seller-banners/' . $seller->id;

    if (!file_exists(public_path($folder))) {
        mkdir(public_path($folder), 0777, true);
    }

    $images = [];

    // ================= UPLOAD LOOP =================
    foreach ($request->file('banner_image') as $index => $file) {

        // 🔥 UNIQUE NAME (NO COLLISION)
        $filename = time().'_'.$index.'_'.uniqid().'.'.$file->getClientOriginalExtension();

        $file->move(public_path($folder), $filename);

        $images[] = $folder . '/' . $filename;
    }

    // ================= SAVE =================
    $banner = SellerBanner::create([
        'seller_id'        => $seller->id,
        'banner_image'     => $images, // ✅ AUTO JSON (if cast = array)
        'banner_title'     => $request->banner_title,
        'banner_sub_title' => $request->banner_sub_title,
        'status'           => 'pending'
    ]);

    // ================= NOTIFY =================
    $this->notify(
        'banner',
        count($images) . ' new banner image(s) uploaded: "' . $request->banner_title . '"',
        'seller.marketplace.banner.list'
    );

    if ($request->ajax()) {
        return response()->json(['status' => true, 'message' => 'Banner Saved Successfully']);
    }

    return redirect()
        ->route('seller.marketplace.banner.list')
        ->with('success', count($images) . ' Banner Image(s) Uploaded Successfully');
}

public function bannerList()
{
    $sellerId = Auth::guard('seller')->id();

    $banners = SellerBanner::where('seller_id',$sellerId)
                ->latest()
                ->get();

    $progress = $this->marketplaceProgress();
    $currentStep = $this->currentStep($progress);
    $verificationStatus = $this->verificationStatus();
    return view(
        'vshop.seller.marketplace.banner-list',
        compact('banners','progress','currentStep','verificationStatus')
    );
}


/* =====================================================
   TEMPLATE
===================================================== */


public function storeTemplate(Request $request)
{
    $sellerId = Auth::guard('seller')->id();

    if (!$sellerId) {
        return redirect()->route('seller.login');
    }

    $request->validate([
        'name' => 'required|max:150',
        'application_type' => 'required',
        'preview_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'description' => 'nullable|max:255'
    ]);

    // ================= NSFW CHECK =================
    if ($request->hasFile('preview_image')) {
        $file = $request->file('preview_image');
        if (!ImageModeratorService::isSafeImage($file->getRealPath())) {
            return back()->withErrors(['preview_image' => 'The preview image contains inappropriate (NSFW) content.'])->withInput();
        }
    }

    // upload image
    $file = $request->file('preview_image');
    $filename = time().'_'.$file->getClientOriginalName();

    if (!file_exists(public_path('templates'))) {
        mkdir(public_path('templates'), 0777, true);
    }

    $file->move(public_path('templates'), $filename);

    $template = WebsiteTemplate::create([
        'seller_id' => $sellerId,
        'name' => $request->name,
        'application_type' => $request->application_type,
        'preview_image' => 'templates/'.$filename,
        'description' => $request->description,
        'status' => 1
    ]);

    // ✅ NOTIFY
    $this->notify(
        'template',
        'Template "' . $template->name . '" Created Successfully',
        'seller.marketplace.template'
    );

    if ($request->ajax()) {
        return response()->json(['status' => true, 'message' => 'Template Configuration Saved']);
    }

    return redirect()
        ->route('seller.marketplace.template')
        ->with('success','Template Created Successfully');
}


public function updateTemplate(Request $request, $id)
{
    $sellerId = Auth::guard('seller')->id();

    $template = WebsiteTemplate::where('seller_id',$sellerId)
        ->findOrFail($id);

    $request->validate([
        'name' => 'required|max:150',
        'application_type' => 'required',
        'preview_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // ================= NSFW CHECK =================
    if ($request->hasFile('preview_image')) {
        $file = $request->file('preview_image');
        if (!ImageModeratorService::isSafeImage($file->getRealPath())) {
            return back()->withErrors(['preview_image' => 'The preview image contains inappropriate (NSFW) content.'])->withInput();
        }
    }

    $data = [
        'name' => $request->name,
        'application_type' => $request->application_type,
        'description' => $request->description,
    ];

    // image update
    if ($request->hasFile('preview_image')) {

        // delete old
        if (!empty($template->preview_image) && file_exists(public_path($template->preview_image))) {
            unlink(public_path($template->preview_image));
        }

        $file = $request->file('preview_image');
        $filename = time().'_'.$file->getClientOriginalName();

        $file->move(public_path('templates'), $filename);

        $data['preview_image'] = 'templates/'.$filename;
    }

    $template->update($data);

    // ✅ NOTIFY
    $this->notify(
        'template',
        'Template "' . $template->name . '" Updated Successfully',
        'seller.marketplace.template'
    );

    return redirect()
        ->route('seller.marketplace.template')
        ->with('success','Template Updated Successfully');
}

public function template()
{
    $sellerId = Auth::guard('seller')->id();

    $progress = $this->marketplaceProgress();
    $currentStep = $this->currentStep($progress);

    if(!$progress['banner']){
        return redirect()->route('seller.marketplace.banner');
    }

    // ✅ GET ALL TEMPLATES (ADMIN + SELLER)
    $templates = WebsiteTemplate::where(function($q) use ($sellerId){
        $q->whereNull('seller_id')
          ->orWhere('seller_id',$sellerId);
    })->where('status',1)->get();

    // ✅ MULTI SELECT FIX (IMPORTANT)
    $selectedTemplates = \DB::table('seller_website_templates')
        ->where('seller_id', $sellerId)
        ->pluck('template_id')
        ->toArray();
    $verificationStatus = $this->verificationStatus();
    return view('vshop.seller.marketplace.template', compact(
        'templates','selectedTemplates','progress','currentStep','verificationStatus'
    ));
}

public function saveTemplate(Request $request)
{
    $sellerId = auth()->guard('seller')->id();

    $request->validate([
        'templates' => 'required|array',
        'templates.*' => 'exists:website_templates,id',
    ]);

    // 🔥 DELETE OLD
    \DB::table('seller_website_templates')
        ->where('seller_id', $sellerId)
        ->delete();

    // 🔥 INSERT NEW
    foreach ($request->templates as $templateId) {

        \DB::table('seller_website_templates')->insert([
            'seller_id' => $sellerId,
            'template_id' => $templateId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // ✅ NOTIFY
    $this->notify(
        'template',
        count($request->templates) . ' template(s) selected for your marketplace',
        'seller.marketplace.seo'
    );

    if ($request->ajax()) {
        return response()->json(['status' => true, 'message' => 'SEO Settings Saved']);
    }

    return redirect()
        ->route('seller.marketplace.seo')
        ->with('success','Templates Saved Successfully');
}

public function deleteTemplate($id)
{
    $sellerId = Auth::guard('seller')->id();

    $template = WebsiteTemplate::where('seller_id',$sellerId)
        ->findOrFail($id);
    
    $templateName = $template->name;

    if(file_exists(public_path($template->preview_image))){
        unlink(public_path($template->preview_image));
    }

    $template->delete();

    // âœ… NOTIFY
    $this->notify(
        'template',
        'Template "' . $templateName . '" Deleted Successfully',
        'seller.marketplace.template'
    );

    return back()->with('success','Template Deleted Successfully');
}

public function createTemplate()
{
    $progress = $this->marketplaceProgress();
    $currentStep = $this->currentStep($progress);
    $verificationStatus = $this->verificationStatus();
    return view('vshop.seller.marketplace.template-create',compact('progress','currentStep','verificationStatus'));
}

public function editTemplate($id)
{
    $sellerId = Auth::guard('seller')->id();

    $template = WebsiteTemplate::where('seller_id',$sellerId)
        ->findOrFail($id);

    $progress = $this->marketplaceProgress();
    $currentStep = $this->currentStep($progress);
    $verificationStatus = $this->verificationStatus();
    return view('vshop.seller.marketplace.template-edit', compact('template','progress','currentStep','verificationStatus'));
}

/* =====================================================
   SEO
===================================================== */

public function seo()
{
    $progress=$this->marketplaceProgress();
    $currentStep=$this->currentStep($progress);

    if(!$progress['template'])
        return redirect()->route('seller.marketplace.template');

    $seo = null;
    $verificationStatus = $this->verificationStatus();
    return view(
        'vshop.seller.marketplace.seo',
        compact('seo','progress','currentStep','verificationStatus')
    );
}

public function editSeo($id)
{
    $progress=$this->marketplaceProgress();
    $currentStep=$this->currentStep($progress);

    $seo = SellerSeo::findOrFail($id);
    $verificationStatus = $this->verificationStatus();
    return view(
        'vshop.seller.marketplace.seo',
        compact('seo','progress','currentStep','verificationStatus')
    );
}


public function saveSeo(Request $request)
{
    $request->validate([
        'home_title'=>'required',
        'meta_description'=>'required'
    ]);

    $seo = SellerSeo::updateOrCreate(
        ['id'=>$request->id],
        [
            'seller_id'=>Auth::guard('seller')->id(),
            'home_title'=>$request->home_title,
            'meta_description'=>$request->meta_description,
            'meta_keywords'=>$request->meta_keywords,
            'search_hashtags'=>$request->search_hashtags
        ]
    );

    // âœ… NOTIFY
    $this->notify(
        'seo',
        'SEO Settings "' . $seo->home_title . '" Updated Successfully',
        'seller.marketplace.seo.list'
    );

    if ($request->ajax()) {
        return response()->json(['status' => true, 'message' => 'SEO Settings Saved']);
    }

    return redirect()
        ->route('seller.marketplace.seo.list')
        ->with('success','SEO Saved Successfully');
}

public function seoList()
{
    $seos = SellerSeo::where(
        'seller_id',
        Auth::guard('seller')->id()
    )->latest()->get();

    $progress = $this->marketplaceProgress();
    $currentStep = $this->currentStep($progress);
    $verificationStatus = $this->verificationStatus();
    return view(
        'vshop.seller.marketplace.seo-list',
        compact('seos','progress','currentStep','verificationStatus')
    );
}


public function deleteSeo($id)
{
    $seo = SellerSeo::findOrFail($id);
    $seoTitle = $seo->home_title;
    $seo->delete();

    // NOTIFICATION
    $this->notify(
        'seo',
        'SEO Settings "' . $seoTitle . '" Deleted Successfully',
        'seller.marketplace.seo.list'
    );

    return back()->with('success','SEO Deleted Successfully');
}

/* =====================================================
   AREA
===================================================== */

public function area()
{
    $progress = $this->marketplaceProgress();
    $currentStep = $this->currentStep($progress);

    if(!$progress['seo']){
        return redirect()->route('seller.marketplace.seo');
    }

    $countries = Country::select('id','name')
        ->orderBy('name')
        ->get();
    $verificationStatus = $this->verificationStatus();
    return view(
        'vshop.seller.marketplace.area',
        compact('countries','progress','currentStep','verificationStatus')
    );
}

public function getStates($countryId)
{
    return State::where('country_id',$countryId)
        ->select('id','name')
        ->orderBy('name')
        ->get();
}

public function getDistricts($stateId)
{
    return City::where('state_id',$stateId)
        ->select('id','name')
        ->orderBy('name')
        ->get();
}



public function saveArea(Request $request)
{
    $request->validate([
        'addresses' => 'required|array|min:1',
        'addresses.*.country_id' => 'required',
    ]);

    $sellerId = Auth::guard('seller')->id();

    /*
    |--------------------------------------------------------------------------
    | SINGLE GROUP ID
    |--------------------------------------------------------------------------
    */
    $groupId = time();
    $areaCount = 0;

    foreach ($request->addresses as $address) {

        $countryId   = $address['country_id'] ?? null;

        $locationType = $address['location_type'] ?? 'all';

        if (!$countryId) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | ALL COUNTRY
        |--------------------------------------------------------------------------
        */
        if ($locationType == 'all') {

            // CHECK DUPLICATE
            $exists = SellerOperationArea::where([
                'seller_id'     => $sellerId,
                'country_id'    => $countryId,
                'location_type' => 'all',
            ])->exists();

            if (!$exists) {

                SellerOperationArea::create([
                    'seller_id'     => $sellerId,
                    'group_id'      => $groupId,
                    'country_id'    => $countryId,
                    'state_id'      => null,
                    'city_id'       => null,
                    'location_type' => 'all',
                ]);
                $areaCount++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SPECIFIC LOCATION
        |--------------------------------------------------------------------------
        */
        if (
            $locationType == 'specific' &&
            !empty($address['districts'])
        ) {

            foreach ($address['districts'] as $stateId => $cities) {

                if (!is_array($cities)) {
                    continue;
                }

                foreach ($cities as $cityId) {

                    if (!$cityId) {
                        continue;
                    }

                    // CHECK DUPLICATE
                    $exists = SellerOperationArea::where([
                        'seller_id'     => $sellerId,
                        'country_id'    => $countryId,
                        'state_id'      => $stateId,
                        'city_id'       => $cityId,
                        'location_type' => 'specific',
                    ])->exists();

                    if (!$exists) {

                        SellerOperationArea::create([
                            'seller_id'     => $sellerId,
                            'group_id'      => $groupId,
                            'country_id'    => $countryId,
                            'state_id'      => $stateId,
                            'city_id'       => $cityId,
                            'location_type' => 'specific',
                        ]);
                        $areaCount++;
                    }
                }
            }
        }
    }

    // NOTIFICATION
    $this->notify(
        'area',
        $areaCount . ' operational area(s) added successfully',
        'seller.marketplace.area.list'
    );

    if ($request->ajax()) {
        return response()->json(['status' => true, 'message' => 'Area of Operation Saved']);
    }

    return redirect()
        ->route('seller.marketplace.area.list')
        ->with(
            'success',
            'Area Saved Successfully'
        );
}


public function areaList()
{
    $progress = $this->marketplaceProgress();

    $currentStep = $this->currentStep($progress);

    $sellerId = Auth::guard('seller')->id();

    /*
    |--------------------------------------------------------------------------
    | GET ALL AREA GROUPS
    |--------------------------------------------------------------------------
    */
    $areas = SellerOperationArea::with([
            'country',
            'state',
            'city'
        ])
        ->where('seller_id', $sellerId)
        ->latest('group_id')
        ->get()

        /*
        |--------------------------------------------------------------------------
        | GROUP BY GROUP ID
        |--------------------------------------------------------------------------
        */
        ->groupBy('group_id')

        /*
        |--------------------------------------------------------------------------
        | THEN COUNTRY GROUP
        |--------------------------------------------------------------------------
        */
        ->map(function ($group) {

            return $group->groupBy('country_id');

        });

    $verificationStatus = $this->verificationStatus();

    return view(
        'vshop.seller.marketplace.list',
        compact(
            'areas',
            'progress',
            'currentStep',
            'verificationStatus'
        )
    );
}

public function editArea($groupId)
{
    $sellerId = Auth::guard('seller')->id();

    /*
    |--------------------------------------------------------------------------
    | GET GROUP DATA
    |--------------------------------------------------------------------------
    */
    $areas = SellerOperationArea::with([
            'state',
            'city',
            'country'
        ])
        ->where('seller_id', $sellerId)
        ->where('group_id', $groupId)
        ->get();

    if ($areas->isEmpty()) {
        abort(404);
    }

    $countries = Country::select('id', 'name')
        ->orderBy('name')
        ->get();

    $progress = $this->marketplaceProgress();

    $currentStep = $this->currentStep($progress);

    $verificationStatus = $this->verificationStatus();

    return view(
        'vshop.seller.marketplace.area_edit',
        compact(
            'areas',
            'countries',
            'progress',
            'currentStep',
            'verificationStatus',
            'groupId'
        )
    );
}


public function updateArea(Request $request, $groupId)
{
    $request->validate([
        'addresses' => 'required|array|min:1',
        'addresses.*.country_id' => 'required',
    ], [
        'addresses.required' => 'Please select at least one area.',
        'addresses.*.country_id.required' => 'Country is required.',
    ]);

    $sellerId = Auth::guard('seller')->id();

    /*
    |--------------------------------------------------------------------------
    | DELETE OLD GROUP DATA ONLY
    |--------------------------------------------------------------------------
    */
    SellerOperationArea::where('seller_id', $sellerId)
        ->where('group_id', $groupId)
        ->delete();

    $areaCount = 0;

    /*
    |--------------------------------------------------------------------------
    | INSERT UPDATED DATA
    |--------------------------------------------------------------------------
    */
    foreach ($request->addresses as $address) {

        $countryId = $address['country_id'] ?? null;

        $locationType = $address['location_type'] ?? 'all';

        if (!$countryId) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | ALL LOCATION
        |--------------------------------------------------------------------------
        */
        if ($locationType == 'all') {

            SellerOperationArea::create([
                'seller_id'     => $sellerId,
                'group_id'      => $groupId,
                'country_id'    => $countryId,
                'state_id'      => null,
                'city_id'       => null,
                'location_type' => 'all',
            ]);
            $areaCount++;
        }

        /*
        |--------------------------------------------------------------------------
        | SPECIFIC LOCATION
        |--------------------------------------------------------------------------
        */
        if (
            $locationType == 'specific' &&
            !empty($address['districts'])
        ) {

            foreach ($address['districts'] as $stateId => $cities) {

                if (!is_array($cities)) {
                    continue;
                }

                foreach ($cities as $cityId) {

                    if (!$cityId) {
                        continue;
                    }

                    SellerOperationArea::create([
                        'seller_id'     => $sellerId,
                        'group_id'      => $groupId,
                        'country_id'    => $countryId,
                        'state_id'      => $stateId,
                        'city_id'       => $cityId,
                        'location_type' => 'specific',
                    ]);
                    $areaCount++;
                }
            }
        }
    }

    // NOTIFICATION
    $this->notify(
        'area',
        $areaCount . ' operational area(s) updated successfully',
        'seller.marketplace.area.list'
    );

    return redirect()
        ->route('seller.marketplace.area.list')
        ->with('success', 'Area Updated Successfully');
}


public function deleteArea($groupId)
{
    $sellerId = Auth::guard('seller')->id();

    /*
    |--------------------------------------------------------------------------
    | DELETE FULL GROUP
    |--------------------------------------------------------------------------
    */
    SellerOperationArea::where('seller_id', $sellerId)
        ->where('group_id', $groupId)
        ->delete();

    // NOTIFICATION
    $this->notify(
        'area',
        'Operational area group deleted successfully',
        'seller.marketplace.area.list'
    );

    return redirect()
        ->route('seller.marketplace.area.list')
        ->with('success', 'Area Deleted Successfully');
}



public function createCategory(Request $request, $id = null)
{
    $progress = $this->marketplaceProgress();
    $currentStep = $this->currentStep($progress);

    $sellerId = auth()->guard('seller')->id();

    // Show only admin-created categories (seller_id = null) OR those owned by this seller
    $categories = MarketplaceCategory::where(function ($query) use ($sellerId) {
        $query->whereNull('seller_id')
              ->orWhere('seller_id', $sellerId);
    })->get();

    $category = null;
    $subcategories = [];

    $id = $id ?? $request->route('id');

    if ($id) {
        $category = MarketplaceCategory::find($id);
        // Security: only allow editing if the seller owns this category
        if ($category && $category->seller_id !== null && $category->seller_id != $sellerId) {
            abort(403, 'Unauthorized action.');
        }
        $subcategories = MarketplaceSubCategory::where('category_id', $id)
                            ->pluck('name')
                            ->toArray();
    }

    return view('vshop.seller.marketplace.create-category', compact(
        'progress',
        'currentStep',
        'categories',
        'category',
        'subcategories'
    ));
}

// ─────────────────────────────────────────────────────────────
//  STORE (handles dropdown selection & duplication)
// ─────────────────────────────────────────────────────────────
public function storeCategory(Request $request)
{
    $request->validate([
        'type'          => 'required|in:product,service',
        'category_id'   => 'required|exists:marketplace_categories,id',
        'subcategories' => 'required|array',
        'subcategories.*' => 'required|max:150'
    ]);

    $sellerId = auth()->guard('seller')->id();

    if (!$sellerId) {
        return back()->with('error', 'Seller not logged in');
    }

    DB::beginTransaction();

    try {
        // Find the selected category
        $selectedCategory = MarketplaceCategory::findOrFail($request->category_id);

        // Determine which category to attach subcategories to
        if ($selectedCategory->seller_id === null || $selectedCategory->seller_id != $sellerId) {
            // Admin category OR belongs to another seller → duplicate it for this seller
            $newCategory = MarketplaceCategory::create([
                'name'      => $selectedCategory->name,
                'type'      => $selectedCategory->type,
                'status'    => 1,
                'seller_id' => $sellerId,
                'slug'      => null, // or generate from name
            ]);
            $categoryId = $newCategory->id;
        } else {
            // Already owned by this seller → use it directly
            $categoryId = $selectedCategory->id;
        }

        // Save subcategories under the (new or existing) category
        foreach ($request->subcategories as $subName) {
            if (!empty($subName)) {
                MarketplaceSubCategory::create([
                    'category_id' => $categoryId,
                    'name'        => $subName,
                    'status'      => 1,
                    'seller_id'   => $sellerId, // store seller_id on subcategories too (optional)
                ]);
            }
        }

        DB::commit();

        // Notification (using the new category name if duplicated, else the original)
        $finalCategory = MarketplaceCategory::find($categoryId);
        $this->notify(
            'marketplace',
            'Category "' . $finalCategory->name . '" with ' . count($request->subcategories) . ' sub-categories created successfully',
            'seller.marketplace.category'
        );

        return redirect()
            ->route('seller.marketplace.category')
            ->with('success', 'Subcategories added successfully.');

    } catch (\Exception $e) {
        DB::rollback();
        return back()->with('error', $e->getMessage());
    }
}

public function updateCategory(Request $request, $id)
{
    $sellerId = Auth::guard('seller')->id();

    $request->validate([
        'type'            => 'required|in:product,service',
        'category_id'     => 'required|exists:marketplace_categories,id',
        'subcategories'   => 'required|array|min:1',
        'subcategories.*' => 'required|max:150'
    ]);

    DB::beginTransaction();

    try{

        $selectedCategory = MarketplaceCategory::findOrFail($request->category_id);
        $categoryName = $selectedCategory->name;
        $categoryType = $selectedCategory->type;
        $subCount = count($request->subcategories);

        /*
        |--------------------------------------------------------------------------
        | CATEGORY UPDATE
        |--------------------------------------------------------------------------
        */

        MarketplaceCategory::where('id',$id)
            ->update([

                'name' => $categoryName,
                'type' => $categoryType
            ]);


        /*
        |--------------------------------------------------------------------------
        | OLD SUB IDS
        |--------------------------------------------------------------------------
        */

        $oldSubIds = MarketplaceSubCategory::where(
            'category_id',
            $id
        )->pluck('id')->toArray();


        /*
        |--------------------------------------------------------------------------
        | DELETE ONLY CURRENT SELLER RELATION
        |--------------------------------------------------------------------------
        */

        SellerMarketplaceCategory::where(
            'seller_id',
            $sellerId
        )
        ->where('category_id',$id)
        ->delete();


        /*
        |--------------------------------------------------------------------------
        | DELETE OLD SUBCATEGORIES
        |--------------------------------------------------------------------------
        */

        MarketplaceSubCategory::where(
            'category_id',
            $id
        )->delete();


        /*
        |--------------------------------------------------------------------------
        | CREATE NEW SUBCATEGORIES
        |--------------------------------------------------------------------------
        */

        foreach($request->subcategories as $sub){

            $sub = trim($sub);

            if(!$sub){
                continue;
            }

            $newSub = MarketplaceSubCategory::create([

                'category_id' => $id,

                'name' => $sub,

                'status' => 1
            ]);


            /*
            |--------------------------------------------------------------------------
            | RESTORE SELLER RELATION
            |--------------------------------------------------------------------------
            */

            SellerMarketplaceCategory::create([

                'seller_id'       => $sellerId,

                'category_id'     => $id,

                'sub_category_id' => $newSub->id,

                'created_at'      => now(),

                'updated_at'      => now()
            ]);
        }

        DB::commit();

        // NOTIFICATION
        $this->notify(
            'marketplace',
            'Category "' . $categoryName . '" updated with ' . $subCount . ' sub-categories',
            'seller.marketplace.category.list'
        );

        return redirect()
            ->route('seller.marketplace.category.list')
            ->with(
                'success',
                'Category updated successfully'
            );

    }catch(\Exception $e){

        DB::rollback();

        return back()->with(
            'error',
            $e->getMessage()
        );
    }
}

private function getModuleType()
{
    /*
    |--------------------------------------------------------------------------
    | SESSION PANELS
    |--------------------------------------------------------------------------
    */

    $panels = session('selected_panels', []);

    // SAFE ARRAY
    if(!is_array($panels)){
        $panels = [];
    }

    /*
    |--------------------------------------------------------------------------
    | FLAGS
    |--------------------------------------------------------------------------
    */

    $hasProduct = in_array('product', $panels);

    $hasTravel  = in_array('travel', $panels);

    $hasCourse  = in_array('vcampus', $panels);

    /*
    |--------------------------------------------------------------------------
    | COURSE ONLY
    |--------------------------------------------------------------------------
    */

    if(
        $hasCourse
        &&
        !$hasProduct
        &&
        !$hasTravel
    ){
        return 'course';
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT ONLY
    |--------------------------------------------------------------------------
    */

    if(
        $hasProduct
        &&
        !$hasTravel
        &&
        !$hasCourse
    ){
        return 'product';
    }

    /*
    |--------------------------------------------------------------------------
    | SERVICE ONLY
    |--------------------------------------------------------------------------
    */

    if(
        !$hasProduct
        &&
        $hasTravel
        &&
        !$hasCourse
    ){
        return 'service';
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT + SERVICE
    |--------------------------------------------------------------------------
    */

    if(
        $hasProduct
        &&
        $hasTravel
        &&
        !$hasCourse
    ){
        return 'both';
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT + COURSE
    |--------------------------------------------------------------------------
    */

    if(
        $hasProduct
        &&
        $hasCourse
        &&
        !$hasTravel
    ){
        return 'product_course';
    }

    /*
    |--------------------------------------------------------------------------
    | SERVICE + COURSE
    |--------------------------------------------------------------------------
    */

    if(
        $hasTravel
        &&
        $hasCourse
        &&
        !$hasProduct
    ){
        return 'service_course';
    }

    /*
    |--------------------------------------------------------------------------
    | ALL MODULES
    |--------------------------------------------------------------------------
    */

    if(
        $hasProduct
        &&
        $hasTravel
        &&
        $hasCourse
    ){
        return 'all';
    }

    /*
    |--------------------------------------------------------------------------
    | DEFAULT
    |--------------------------------------------------------------------------
    */

    return 'product';
}

public function generateBannerAI(\Illuminate\Http\Request $request)
{
    $apiKey = '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';

    if (empty($apiKey)) {
        return response()->json(['success' => false, 'message' => 'API Key not configured.']);
    }

    $base64Image = $request->base64Image;
    $oldImage = $request->oldImage;

    // Convert old image path to base64 if base64Image is not provided
    if (!$base64Image && $oldImage) {
        $path = public_path($oldImage);
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    }

    if (!$base64Image) {
        return response()->json(['success' => false, 'message' => 'No image found for analysis. Please upload or select an image first.']);
    }

    try {
        $prompt = "Analyze this banner image and provide a catchy, compelling Title (max 10 words) and a compelling Subtitle (max 20 words) suitable for an e-commerce marketplace banner. Return strictly valid JSON format with exactly two keys: 'title' and 'subtitle'. Do not include markdown code blocks.";

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json'
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'openai/gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user', 
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        ['type' => 'image_url', 'image_url' => ['url' => $base64Image]]
                    ]
                ]
            ],
            'response_format' => ['type' => 'json_object']
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';
            $content = str_replace(['```json', '```'], '', $content);
            $parsed = json_decode(trim($content), true);

            if ($parsed) {
                return response()->json([
                    'success' => true,
                    'title' => $parsed['title'] ?? '',
                    'subtitle' => $parsed['subtitle'] ?? ''
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to generate AI content. ' . $response->body()
        ]);

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("Banner AI Generate Error: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
}