<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ClientController;
use App\Http\Controllers\admin\ReportController;
use App\Http\Controllers\admin\PaymentFinanceController;
use App\Http\Controllers\admin\GeneralSettingsController;
use App\Http\Controllers\admin\AttributeController;
use App\Http\Controllers\admin\AboutSectionController;
use App\Http\Controllers\admin\SliderController;
use App\Http\Controllers\admin\NewsHighlightController;
use App\Http\Controllers\admin\PartnerController;

use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\RefundController;
use App\Http\Controllers\admin\EnquiryController;
use App\Http\Controllers\admin\TrackingToolsController;
use App\Http\Controllers\admin\CouponController;
use App\Http\Controllers\admin\NotificationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\AdminTicketController;
use App\Http\Controllers\admin\BlogController;
use App\Http\Controllers\admin\BlogCategoryController;
use App\Http\Controllers\admin\ActivityLogController;
use App\Http\Controllers\admin\AdminReviewController;
use App\Http\Controllers\admin\ApprovalController;
use App\Http\Controllers\admin\WebsiteTemplateController;
use App\Http\Controllers\admin\AdvertisementController;
use App\Http\Controllers\admin\PageController;
use App\Http\Controllers\admin\SmartQuestionController;
use App\Http\Controllers\admin\SellermanagementController;
use App\Http\Controllers\admin\AdminFinanceController;
use App\Http\Controllers\admin\AdminMarketplaceController;
use App\Http\Controllers\admin\AdminReportAnalyticController;
use App\Http\Controllers\admin\WalletController;
use App\Http\Controllers\admin\StaffController;

use App\Http\Controllers\admin\SettingsController;
use App\Http\Controllers\admin\GrievanceController;



Route::get('/clear-cache', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        return "Cache, config, view, and routes cleared successfully!";
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});

Route::get('/fix-storage-link', function() {
    try {
        $publicStorage = public_path('storage');
        $oldStorage = public_path('storage_old_' . time());
        $output = "";

        if (file_exists($publicStorage) && !is_link($publicStorage)) {
            rename($publicStorage, $oldStorage);
            $output .= "Renamed existing public/storage to " . basename($oldStorage) . ".<br>";
        }

        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $output .= \Illuminate\Support\Facades\Artisan::output();

        return $output;
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});

Route::get('/fix-db-payment', function() {
    \Illuminate\Support\Facades\DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method VARCHAR(50) NULL");
    return "Fixed DB schema successfully";
});

Route::get('/test-tx', function() {
    try {
        $txs = \App\Models\WalletTransaction::latest()->take(5)->get()->toArray();
        $cols = \Illuminate\Support\Facades\Schema::getColumnListing('wallet_transactions');
        $output = [
            'columns' => $cols,
            'latest_transactions' => $txs
        ];
        file_put_contents(public_path('test_tx_output.json'), json_encode($output, JSON_PRETTY_PRINT));
        return "Dumped successfully to public/test_tx_output.json";
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});

//Frontend
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\StaticPageController;
use App\Http\Controllers\Frontend\EmployeeLoginController;
use App\Http\Controllers\Frontend\FrontendProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\AddressController;
use App\Http\Controllers\Frontend\MyorderController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\Frontend\PortfolioController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\FrontendBlogController;

//vtravel project
use App\Http\Controllers\vtravel\HomeController;
use App\Http\Controllers\vtravel\TravelProductController;
use App\Http\Controllers\vtravel\VtravelOrderController;
use App\Http\Controllers\vtravel\VtravelCartController;
use App\Http\Controllers\vtravel\VtravelPaymentController;
use App\Http\Controllers\vtravel\VtravelAddressController;
use App\Http\Controllers\vtravel\VtravelReviewController;
use App\Http\Controllers\vtravel\CustomerProfileController;
use App\Http\Controllers\vtravel\VtravelWishlistController;



use App\Http\Controllers\Shop\VshopProductController;
use App\Http\Controllers\Shop\VshopOrderController;
use App\Http\Controllers\Shop\VshopCartController;
use App\Http\Controllers\Shop\VshopPaymentController;
use App\Http\Controllers\Shop\VshopAddressController;
use App\Http\Controllers\Shop\VshopReviewController;
use App\Http\Controllers\Shop\VshopWishlistController;
use App\Http\Controllers\Shop\SellerAuthController;
use App\Http\Controllers\Shop\MyAccountController;
use App\Http\Controllers\Shop\SellerProductController;
use App\Http\Controllers\Shop\SellerCategoryController;
use App\Http\Controllers\Shop\SellerAttributeController;
use App\Http\Controllers\Shop\SellerOrderController;
use App\Http\Controllers\Shop\SellerReferralController;
use App\Http\Controllers\Shop\SellerReportController;
use App\Http\Controllers\Shop\VshopRatingController;
use App\Http\Controllers\Shop\ShopMyProfileController;
use App\Http\Controllers\Shop\MarketplaceController;
use App\Http\Controllers\Shop\FinanceController;
use App\Http\Controllers\Shop\StoreController;
use App\Http\Controllers\Shop\SellerEnquiryController;
use App\Http\Controllers\Shop\SellerCustomerController;
use App\Http\Controllers\Shop\SellerFinanceController;
use App\Http\Controllers\Shop\CustomerEnquiryController;
use App\Http\Controllers\Shop\CustomerGrievanceController;
use App\Http\Controllers\ChatController;

 


Route::get('/reset-storage', function () {
    $link = public_path('storage');
    $target = storage_path('app/public');

    // Remove old link or folder
    if (is_dir($link)) {
        \File::deleteDirectory($link);
    } elseif (file_exists($link)) {
        unlink($link);
    }

    \File::copyDirectory($target, $link);

    return "✔ Storage reset (copied because symlink is disabled on server).";
});

// Route::get('/', [HomeController::class, 'Home'])->name('dashboard');

Route::get('/', [HomeController::class, 'vshophome'])->name('home');
Route::get('/visual-showcase', [HomeController::class, 'visuals'])->name('vshop.visuals');
Route::post('/spin-wheel', [\App\Http\Controllers\vtravel\SpinController::class, 'spin'])->name('spin.wheel');
Route::get('/VtravelHome', [HomeController::class, 'vtravelhome'])->name('vtravelhome');
Route::get('/discount-products', [HomeController::class, 'discountProducts'])
    ->name('discount.products');
  Route::get('/customer/counts', [HomeController::class, 'getCounts'])->name('customer.counts');

 Route::get('/search', [HomeController::class, 'search'])->name('vshop.search');
  
Route::get('/vtravellogin', [EmployeeLoginController::class, 'showVtravelLogin'])
    ->name('employeelogin');
Route::post('/customerlogin', [EmployeeLoginController::class, 'vtravelLogin'])
    ->name('customer.login');
Route::get('/vtravelregister', [EmployeeLoginController::class, 'showVtravelRegister'])
    ->name('customer.register.form');
Route::post('/vtravelregister', [EmployeeLoginController::class, 'vtravelRegister'])
    ->name('customer.register');
Route::post('/vtravellogout', [EmployeeLoginController::class, 'logout'])
    ->name('logout');
 Route::get('/vtravelproduct', [TravelProductController::class, 'index'])->name('vtravelproduct.index');
  Route::get('/products/{id}/details', [TravelProductController::class, 'show'])->name('products.show');
  
   Route::post('/customer/notifications/clear', [App\Http\Controllers\CustomerNotificationController::class, 'clear'])
                ->name('customer.notifications.clear');
 //seller 
  
Route::prefix('seller')->name('seller.')->group(function () {

    Route::get('/register', [SellerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [SellerAuthController::class, 'storeRegister'])->name('register.store');
    Route::post('/register/verify-otp', [SellerAuthController::class, 'verifyRegisterOtp'])->name('register.verify');
    Route::post('/register/resend-otp', [SellerAuthController::class, 'resendRegisterOtp'])->name('register.resend');

    Route::get('/login', [SellerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [SellerAuthController::class, 'login'])->name('login.post');
    Route::post('login/send-otp', [SellerAuthController::class, 'sendLoginOtp'])
    ->name('login.otp');

    Route::post('login/verify-otp', [SellerAuthController::class, 'verifyLoginOtp'])
    ->name('login.verify');

    Route::post('login/verify-email-otp', [SellerAuthController::class, 'verifyLoginEmailOtp'])
    ->name('login.verify.email');
    
    Route::get('/forgot-password', [SellerAuthController::class, 'forgotPassword'])
    ->name('forgot.password');
    
    Route::post('/switch-panel-multi', [SellerAuthController::class, 'switchPanelMulti'] )->name('switch.panel.multi');


Route::post('forgot-password/send-otp', [SellerAuthController::class, 'sendForgotOtp']);

Route::post('/forgot-password/verify-otp', [SellerAuthController::class, 'verifyForgotOtp']);

Route::post('/forgot-password/reset', [SellerAuthController::class, 'resetPassword']);

Route::get('/global-search', [SellerAuthController::class, 'index'])
->name('global.search');



    Route::middleware('auth:seller')->group(function () {
        
    Route::get('/panel-select', [SellerAuthController::class, 'panelSelect'])
    ->name('panel.select');
    
Route::get('/switch-panel/{type}', function ($type) {

    session(['seller_panel' => [$type]]); // ðŸ”¥ store as array

    return redirect()->route('seller.dashboard');

})->name('seller.switch.panel')->middleware('auth:seller');

        Route::get('/dashboard', [SellerAuthController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [SellerAuthController::class, 'logout'])->name('logout');

        // Seller notifications routes
        Route::get('/notifications', [SellerAuthController::class,'getNotifications']);
        Route::post('/notifications/read', [SellerAuthController::class,'markRead']);
        Route::post('/notifications/clear', [SellerAuthController::class,'clear'])->name('notifications.clear');

        // Customer notifications routes (protected by customer auth)
        Route::middleware('auth:customer')->group(function () {
            Route::get('/customer/notifications', [App\Http\Controllers\CustomerNotificationController::class, 'index'])
                ->name('customer.notifications');
           
            // Duplicate GET route removed - use POST for clearing notifications
        });

        Route::get('/finance',[FinanceController::class,'index'])->name('finance.index');
        Route::get('/grievance', [GrievanceController::class, 'index'])->name('grievance.index');
        

Route::prefix('products')->name('products.')->group(function () {

Route::get('/',[SellerProductController::class,'index'])->name('index');

Route::get('/mis-dashboard',
[SellerProductController::class,'misDashboard'])->name('mis.dashboard');

Route::post('/save-referral-settings',
[SellerProductController::class,'saveReferralSettings'])->name('save.referral.settings');

        // GET CATEGORIES FOR DEPENDENT DROPDOWN
        Route::get('/get-categories', [SellerProductController::class,'getCategories'])->name('getCategories');
        
        // BOOST PRODUCTS
        Route::get('/boost-products', [SellerProductController::class, 'getBoostProducts'])->name('boost.products');
        Route::post('/boost-submit', [SellerProductController::class, 'submitBoost'])->name('boost.submit');

        // GET SUB-CATEGORIES FOR DEPENDENT DROPDOWN
Route::get(
'/categories/{type}',
[SellerProductController::class,'getCategories']
)->name('categories.byType');

Route::get('/get-subcategories/{id}', [SellerProductController::class,'getSubcategories']);


/*
|--------------------------------------------------------------------------
STEP 1 : BASIC
|--------------------------------------------------------------------------
*/

Route::post('/ai-generate',
[SellerProductController::class,'generateAIContent'])
->name('ai.generate');

Route::post('/seo-ai-generate',
[SellerProductController::class,'generateSeoAIContent'])
->name('seo.ai.generate');

Route::get('/basic',
[SellerProductController::class,'basic'])
->name('basic');

Route::post('/basic',
[SellerProductController::class,'saveBasic'])
->name('basic.save');

Route::get('/basic/{product}',
[SellerProductController::class,'basic'])
->name('basic.edit');

Route::post('/basic/{product}',
[SellerProductController::class,'saveBasic'])
->name('basic.update');

Route::get('/findstates/{country_id}', [SellerProductController::class, 'getStates']);
    Route::get('/findcities/{state_id}', [SellerProductController::class, 'getCities']);

/*
|--------------------------------------------------------------------------
STEP 2 : MEDIA
|--------------------------------------------------------------------------
*/

Route::get('/media',
[SellerProductController::class,'media'])
->name('media');

Route::post('/media',
[SellerProductController::class,'saveMedia'])
->name('media.save');

Route::get('/media/{product}',
[SellerProductController::class,'media'])
->name('media.edit');

Route::post('/media/{product}',
[SellerProductController::class,'saveMedia'])
->name('media.update');



/*
|--------------------------------------------------------------------------
STEP 3 : SPECIFICATIONS
|--------------------------------------------------------------------------
*/

Route::get('/specifications',
[SellerProductController::class,'specifications'])
->name('specifications');

Route::post('/specifications',
[SellerProductController::class,'saveSpecifications'])
->name('specifications.save');

Route::get('/specifications/{product}',
[SellerProductController::class,'specifications'])
->name('specifications.edit');

Route::post('/specifications/{product}',
[SellerProductController::class,'saveSpecifications'])
->name('specifications.update');

Route::post('/specifications/{product}/lead-settings',
[SellerProductController::class,'saveLeadSettings'])
->name('specifications.lead-settings.update');

Route::get('/stock-movements/{product}',
[SellerProductController::class,'getStockMovements'])
->name('specifications.stock-movements')
->middleware('auth:seller');



/*
|--------------------------------------------------------------------------
STEP 4 : SEO
|--------------------------------------------------------------------------
*/

Route::get('/seo',
[SellerProductController::class,'seo'])
->name('seo');

Route::post('/seo',
[SellerProductController::class,'saveSeo'])
->name('seo.save');

Route::get('/seo/{product}',
[SellerProductController::class,'seo'])
->name('seo.edit');

Route::post('/seo/{product}',
[SellerProductController::class,'saveSeo'])
->name('seo.update');



/*
|--------------------------------------------------------------------------
STEP 5 : PRICE
|--------------------------------------------------------------------------
*/

Route::get('/price',
[SellerProductController::class,'price'])
->name('price');

Route::post('/price',
[SellerProductController::class,'savePrice'])
->name('price.save');

Route::get('/price/{product}',
[SellerProductController::class,'price'])
->name('price.edit');

Route::post('/price/{product}',
[SellerProductController::class,'savePrice'])
->name('price.update');

Route::post('/price/{product}/ai-recommend',
[SellerProductController::class,'getAiRecommendation'])
->name('price.ai-recommend');


Route::get('faq/{id}', [SellerProductController::class,'faq'])->name('faq');

Route::post('faq/save/{id}', [SellerProductController::class,'saveFaq'])->name('faq.save');

Route::get('faq/edit/{id}/{faq_id}', [SellerProductController::class,'editFaq'])->name('faq.edit');
Route::post('faq/ai', [SellerProductController::class,'generateFaqAIContent'])->name('faq.ai');

Route::post('faq/update/{id}/{faq_id}', [SellerProductController::class,'updateFaq'])->name('faq.update');

Route::get('faq/delete/{id}/{faq_id}', [SellerProductController::class,'deleteFaq'])->name('faq.delete');
Route::get('faq-list/{id}', [SellerProductController::class,'faqList'])->name('faq.list');



/*
|--------------------------------------------------------------------------
STEP 6 : LIST / PREVIEW
|--------------------------------------------------------------------------
*/

Route::get('/list/{product}',
[SellerProductController::class,'productList'])
->name('list');



/*
|--------------------------------------------------------------------------
STEP 7 : REVIEW
|--------------------------------------------------------------------------
*/

Route::get('/review/',
[SellerProductController::class,'review'])
->name('reviews');

Route::post('/review/{id}/reply', [SellerProductController::class, 'replyReview'])->name('reviews.reply');

Route::get(
    '{id}/review',
    [SellerProductController::class, 'productReview']
)->name('productsreview');

Route::get('{id}/download', [SellerProductController::class,'download'])->name('download');

Route::post('{id}/download/save', [SellerProductController::class,'saveDownload'])->name('download.save');

Route::get('{id}/download/edit/{download_id}', [SellerProductController::class,'editDownload'])->name('download.edit'); // âœ… ADD THIS

Route::post('{id}/download/update/{download_id}', [SellerProductController::class,'updateDownload'])->name('download.update'); // âœ… ADD THIS

Route::get(
    '{id}/download/delete/{download_id}',
    [SellerProductController::class,'deleteDownload']
)->name('download.delete');

Route::get('{id}/sample', [SellerProductController::class,'sample'])->name('samplecode');
Route::post('{id}/sample/save', [SellerProductController::class,'saveSample'])->name('sample.save');
Route::get('{id}/sample/delete', [SellerProductController::class,'deleteSample'])->name('sample.delete');
Route::get('{id}/sample/edit', [SellerProductController::class,'editSample'])->name('sample.edit');
Route::post('{id}/sample/update', [SellerProductController::class,'updateSample'])->name('sample.update'); 
/*
|--------------------------------------------------------------------------
DELETE PRODUCT
|--------------------------------------------------------------------------
*/

Route::delete('/{product}',
[SellerProductController::class,'destroy'])
->name('destroy');

Route::get(
    '{id}/logs',
    [SellerProductController::class, 'productLogs']
)->name('logs');

Route::get('search-ajax', [SellerProductController::class, 'searchAjax'])->name('search_ajax');
Route::get('{id}/recommendations', [SellerProductController::class, 'getRecommendations'])->name('recommendations.get');
Route::post('{id}/recommendations', [SellerProductController::class, 'saveRecommendations'])->name('recommendations.save');
Route::get('{id}/campaign-participation', [SellerProductController::class, 'getCampaignParticipation'])->name('campaign-participation.get');
Route::post('{id}/campaign-participation', [SellerProductController::class, 'saveCampaignParticipation'])->name('campaign-participation.save');

});






Route::prefix('products')->group(function(){

    Route::get('/bulk', [SellerProductController::class,'bulkindex'])->name('products.bulk');

    Route::post('/bulk-upload', [SellerProductController::class,'bulkUpload'])->name('products.bulk.upload');

    Route::post('/bulk-update', [SellerProductController::class,'bulkUpdate'])->name('products.bulk.update');

    Route::post('/bulk-delete', [SellerProductController::class,'bulkDelete'])->name('products.bulk.delete');

    Route::get('/sample-download', [SellerProductController::class,'downloadSample'])->name('products.sample');

    // Campaign Participation Routes
    Route::get('/campaign-participation/all', [\App\Http\Controllers\Shop\SellerProductController::class, 'getAllCampaignParticipations']);
    Route::post('/campaign-participation/all', [\App\Http\Controllers\Shop\SellerProductController::class, 'saveAllCampaignParticipations']);

});
        
    Route::resource('categories',SellerCategoryController::class);
    
    Route::get('attributes', [SellerAttributeController::class, 'index'])->name('attributes.index');
    Route::post('attributes', [SellerAttributeController::class, 'store'])->name('attributes.store');
    Route::delete('attributes/{id}', [SellerAttributeController::class, 'destroy'])->name('attributes.destroy');

    Route::get('attributes/{id}/terms', [SellerAttributeController::class, 'terms'])->name('attributes.terms');
    Route::post('attributes/{id}/terms', [SellerAttributeController::class, 'termsStore'])->name('attributes.terms.store');
    Route::delete('attributes/{attributeId}/terms/{termId}', [SellerAttributeController::class, 'termsdestroy'])->name('attributes.terms.destroy');
    
    
    //seller order Management
    
           
    Route::get('/orders/payment', [SellerOrderController::class,'payment'])->name('paymenthandle');

    Route::get('/orders/shipping', [SellerOrderController::class,'shipping'])->name('shipping');
    
       Route::post('orders/shipping/save', [SellerOrderController::class, 'saveShipping'])
        ->name('shipping.save');
    
    Route::post('/orders/reminder', [SellerOrderController::class,'sendReminder']);
    
    Route::get('orders/mis', [SellerOrderController::class, 'ordermis'])
    ->name('orders.mis');
    
    Route::post('/orders/sync-courier', [SellerOrderController::class, 'syncCourier'])
    ->name('orders.syncCourier');
    
        Route::get('orders', [SellerOrderController::class, 'index'])->name('orders.index');

        Route::get('orders/processing', [SellerOrderController::class, 'processing'])->name('orders.processing');
        Route::get('orders/completed', [SellerOrderController::class, 'completed'])->name('orders.completed');
        Route::get('orders/out-for-delivery', [SellerOrderController::class, 'outForDelivery'])->name('orders.outfordelivery');
        Route::get('orders/refund', [SellerOrderController::class, 'refund'])->name('orders.refund');
        Route::get('orders/declined', [SellerOrderController::class, 'declined'])->name('orders.declined');
        Route::get('orders/failed', [SellerOrderController::class, 'failed'])->name('orders.failed');
        Route::get('orders/cancelled', [SellerOrderController::class, 'cancelled'])->name('orders.cancelled');
        Route::get('orders/cancel-request', [SellerOrderController::class, 'cancelRequest'])->name('orders.cancelrequest');

        // Referral History
        Route::get('/referrals/history', [SellerReferralController::class, 'history'])->name('referrals.history');

        Route::get('orders/{id}', [SellerOrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{id}/status', [SellerOrderController::class, 'updateStatus'])->name('orders.updateStatus');

        Route::get('orders/{id}/manage', [SellerOrderController::class, 'showManageOrder'])->name('orders.manage');
        Route::get('orders/{id}/invoice', [SellerOrderController::class, 'invoice'])->name('orders.invoice');
         // Cancel order
    Route::get('/orders/cancel/{id}', [SellerOrderController::class, 'cancel'])
        ->name('orders.cancel');

    // Return request
Route::post('/orders/return-request', [SellerOrderController::class, 'returnRequest'])
    ->name('orders.return');
Route::post('/orders/return/cancel-pickup-refund', [SellerOrderController::class, 'cancelPickupAndRefund']);
    
    Route::get('/order/getsubcategories', [SellerOrderController::class,'getSubcategories']);
Route::get('/order/getproducts', [SellerOrderController::class,'getProducts']);
    
Route::post('/orders/return/approve', [SellerOrderController::class, 'approveReturn']);
Route::post('/orders/return/reject', [SellerOrderController::class, 'rejectReturn']);
Route::post('/orders/return/verify', [SellerOrderController::class, 'verifyReturn']);
Route::post('/orders/return/initiate-pickup', [SellerOrderController::class, 'initiatePickup']);
        
        Route::get('/orders/verify-return/{id}', [SellerOrderController::class, 'verifyReturn'])
    ->name('orders.verify-return');
    
      Route::post('/orders/update-tracking/{id}', [SellerOrderController::class, 'updateTracking'])
        ->name('orders.update-tracking');
        
      Route::post('/orders/refund-reason',[SellerOrderController::class,'saveRefundReason']);
      
      Route::get('payout/export/{type}', [SellerFinanceController::class, 'payoutexport'])
      ->name('payout.export');
      
      Route::get('payout/invoice/{id}', [SellerFinanceController::class, 'invoices'])
    ->name('payout.invoice');
      
      Route::post('/check-shipping-live',[SellerOrderController::class,'checkShippingLive']);
 

        //seller order report
        Route::get('reports', [SellerReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export/csv', [SellerReportController::class, 'exportCSV'])->name('reports.export.csv');
        Route::get('reports/export/pdf', [SellerReportController::class, 'exportPDF'])->name('reports.export.pdf');
        
  
        
        
        Route::get('getsubcategories', [SellerOrderController::class, 'getSubcategories'])
    ->name('get.subcategories');
        
        

        Route::get('reports/search-sales', [SellerReportController::class, 'searchSales'])->name('reports.search.sales');
Route::get('/profile',[ShopMyProfileController::class,'index'])
->name('profile');


Route::post('/send-email-otp', [ShopMyProfileController::class, 'sendEmailOtp']);
Route::post('/verify-email-otp', [ShopMyProfileController::class, 'verifyEmailOtp']);



Route::get('/global-search', [ShopMyProfileController::class, 'globalSearch'])
    ->name('global.search');
    
    

Route::get('staff/create', [ShopMyProfileController::class, 'create'])->name('staff.create');
Route::post('staff/store', [ShopMyProfileController::class, 'store'])->name('staff.store');
Route::delete('/staff/delete/{id}', [ShopMyProfileController::class, 'deleteStaff'])
    ->name('staff.delete');

Route::get('/organization-states/{country_id}', [ShopMyProfileController::class, 'getStates']);
Route::get('/organization-cities/{state_id}', [ShopMyProfileController::class, 'getCities']);

Route::post('/my-profile-update',
        [ShopMyProfileController::class,'update']
    )->name('profile.update');
    
Route::get('organization-list',[ShopMyProfileController::class,'organizationList'])->name('organization.list');
Route::get('organization',[ShopMyProfileController::class,'organization'])->name('organization');
Route::post('organization',[ShopMyProfileController::class,'saveOrganization'])->name('organization.save');
Route::delete('organization',[ShopMyProfileController::class,'deleteOrganization'])->name('organization.delete');

Route::post('/send-otp', [ShopMyProfileController::class, 'sendOtp']);

Route::post('/verify-otp', [ShopMyProfileController::class, 'verifyOtpAuto']);

Route::post('/verify-email', [ShopMyProfileController::class, 'verifyEmail']);

Route::post('/org-autosave', [ShopMyProfileController::class, 'orgAutoSave']);

Route::post('/basic-update', [ShopMyProfileController::class, 'updateBasic'])
    ->name('basic.update');
    
    


/* CONTACT */
Route::get('contact-list',[ShopMyProfileController::class,'contactList'])->name('contact.list');
Route::get('contact',[ShopMyProfileController::class,'contact'])->name('contact');
Route::post('contact',[ShopMyProfileController::class,'saveContact']);

Route::delete('/contact-delete/{index}', [ShopMyProfileController::class, 'deleteContact'])
    ->name('contact.delete');

/* BANK */
Route::get('bank-list',[ShopMyProfileController::class,'bankList'])->name('bank.list');
Route::get('bank-details',[ShopMyProfileController::class,'bank'])->name('bank');
Route::post('bank-details',[ShopMyProfileController::class,'saveBank']);
Route::delete('bank-details/delete/{id}',
    [ShopMyProfileController::class,'deleteBank']
)->name('bank.delete');

/* PAYMENT */
Route::get('payment-list',[ShopMyProfileController::class,'paymentList'])->name('payment.list');
Route::get('payment',[ShopMyProfileController::class,'payment'])->name('payment');
Route::post('payment',[ShopMyProfileController::class,'savePayment']);
Route::delete('payment',[ShopMyProfileController::class,'deletePayment'])->name('payment.delete');
Route::post('/payment/status', [ShopMyProfileController::class, 'paymentStatus'])
    ->name('payment.status');

/* AGREEMENT */
Route::get('agreement-list',[ShopMyProfileController::class,'agreementList'])->name('agreement.list');
Route::get('agreement',[ShopMyProfileController::class,'agreement'])->name('agreement');
Route::post('agreement',[ShopMyProfileController::class,'saveAgreement']);
Route::delete('agreement',[ShopMyProfileController::class,'deleteAgreement'])->name('agreement.delete');


Route::get('logs',[ShopMyProfileController::class,'logs'])->name('logs');

//seller marketplace
        
Route::prefix('marketplace')->name('marketplace.')->group(function(){
    Route::get('/setup',[MarketplaceController::class,'setup'])->name('setup');
    Route::get('/category',[MarketplaceController::class,'category'])
    ->name('category');
    
    
Route::get(
        'category/create',
        [MarketplaceController::class,'createCategory']
    )->name('category.create');


Route::get(
'category/edit/{id}',
[MarketplaceController::class,'createCategory']
)->name('category.edit');

Route::post(
    'category/delete',
    [MarketplaceController::class,'deleteCategory']
)->name('category.delete');

Route::post(
    'category/destroy',
    [MarketplaceController::class,'destroyCategory']
)->name('category.destroy');




Route::post(
'category/update/{id}',
[MarketplaceController::class,'updateCategory']
)->name('category.update');

Route::post(
    'category/store',
        [MarketplaceController::class,'storeCategory']
    )->name('category.store');
    
    Route::post('category/save',
[MarketplaceController::class,'saveCategory'])
->name('category.save');


Route::get('category-list',[MarketplaceController::class,'categoryList'])->name('category.list');

Route::get('get-products/{category}',[MarketplaceController::class,'getProducts'])->name('get.products');

Route::get('subcategories/{category}',[MarketplaceController::class,'getSubCategories']);
    
    Route::get('/banner',[MarketplaceController::class,'banner'])
        ->name('banner');
    
    // Banner AI generate
    Route::post('/banner/ai-generate', [MarketplaceController::class, 'generateBannerAI'])
        ->name('banner.ai');
    
    Route::get(
'banner/edit/{id}',
[MarketplaceController::class,'banner']
)->name('banner.edit');

Route::post(
'banner/update/{id}',
[MarketplaceController::class,'bannerUpdate']
)->name('banner.update');

Route::delete(
'banner/delete/{id}',
[MarketplaceController::class,'bannerDelete']
)->name('banner.delete');
    
    Route::get(
'banner-list',[MarketplaceController::class,'bannerList'])->name('banner.list');
    
    Route::post(
'/banner-store',
[MarketplaceController::class,'bannerStore']
)->name('banner.store');


Route::get('/template',[MarketplaceController::class,'template'])->name('template');

Route::post('/template/save',[MarketplaceController::class,'saveTemplate'])->name('template.save');

// CRUD
Route::get('/template/create',[MarketplaceController::class,'createTemplate'])->name('template.create');

Route::post('/template/store',[MarketplaceController::class,'storeTemplate'])->name('template.store');

Route::get('/template/edit/{id}',[MarketplaceController::class,'editTemplate'])->name('template.edit');

Route::post('/template/update/{id}',[MarketplaceController::class,'updateTemplate'])->name('template.update');

Route::delete('/template/delete/{id}',[MarketplaceController::class,'deleteTemplate'])->name('template.delete');

    
Route::get('/marketplaceseo',[MarketplaceController::class,'seo'])->name('seo'); // create page

Route::post('/seo',[MarketplaceController::class,'saveSeo'])->name('seo.save'); // save/update

Route::get('/seo-list',[MarketplaceController::class,'seoList'])->name('seo.list'); // list

// ✅ NEW
Route::get('/seo-edit/{id}',[MarketplaceController::class,'editSeo'])->name('seo.edit');

Route::delete('/seo-delete/{id}',[MarketplaceController::class,'deleteSeo'])->name('seo.delete');

    
Route::post('/area-save',[MarketplaceController::class,'saveArea'])->name('area.save');
    
Route::get('/area',[MarketplaceController::class,'area'])->name('area');
  // LISTING PAGE
Route::get('area-operation/list',[MarketplaceController::class,'areaList'])->name('area.list');

Route::get(
    '/area/edit/{groupId}',
    [MarketplaceController::class,'editArea']
)->name('area.edit');


Route::get('/verify/{id}', [MarketplaceController::class, 'verify'])->name('verify');
Route::post(
    '/area/update/{groupId}',
    [MarketplaceController::class,'updateArea']
)->name('area.update');


Route::get(
    '/area/delete/{groupId}',
    [MarketplaceController::class,'deleteArea']
)->name('area.delete');

Route::get('/get-states/{country}',
    [MarketplaceController::class,'getStates']);

Route::get('/get-districts/{state}',
    [MarketplaceController::class,'getDistricts']);

    
    });    
    

//seller enquiry

Route::get('/mis',[SellerEnquiryController::class,'mis'])->name('enquiry.mis');

Route::get('/list',[SellerEnquiryController::class,'list'])->name('enquiry.list');

  // ✅ ADD NEW
    Route::get('/create', [SellerEnquiryController::class,'create'])->name('enquiry.create');

    Route::post('/store', [SellerEnquiryController::class,'storeManual'])->name('enquiry.store');

Route::post('/update',[SellerEnquiryController::class,'update'])->name('enquiry.update');

Route::get('/subcategories/{cat}', [SellerEnquiryController::class,'getSubCategory']);
Route::get('/products/{sub}', [SellerEnquiryController::class,'getProducts']);

Route::get('enquiry/convert/{id}', [SellerEnquiryController::class, 'convert'])
    ->name('enquiry.convert');

//customer management


Route::get('/customer-mis',[SellerCustomerController::class,'mis'])
->name('customer.mis');

Route::get('/customer-list',[SellerCustomerController::class,'list'])
->name('customer.list');

Route::get('/customer-create',[SellerCustomerController::class,'create'])
->name('customer.create');

Route::post('/store',[SellerCustomerController::class,'store'])
->name('customer.store');

Route::get('/edit/{id}',[SellerCustomerController::class,'edit'])
->name('customer.edit');

Route::get('/customer/logs/{id}', 
    [SellerCustomerController::class, 'getLogs']
    
    
);

Route::get('/city-pincode/{id}', [SellerCustomerController::class,'cityPincode']);

Route::post('/update/{id}',[SellerCustomerController::class,'update'])


->name('customer.update');

Route::delete('/delete/{id}', [SellerCustomerController::class,'delete'])->name('customer.delete');

Route::get('/bulk-upload', [SellerCustomerController::class, 'bulkPage'])->name('customer.bulk.page');

Route::post('/bulk-upload', [SellerCustomerController::class, 'bulkUpload'])->name('customer.bulk.upload');

Route::get('/sample-download', [SellerCustomerController::class, 'downloadSample'])->name('customer.sample');

 Route::get('/block/{id}', [SellerCustomerController::class, 'block'])->name('customer.block');
Route::get('/unblock/{id}', [SellerCustomerController::class, 'unblock'])->name('customer.unblock');

    Route::get('customer/{id}', [SellerCustomerController::class, 'show'])->name('customer.show');


Route::post('/customer/save-log', [SellerCustomerController::class,'saveLog']);


Route::get('states/{country_id}', [SellerCustomerController::class,'getStates']);
Route::get('cities/{state_id}', [SellerCustomerController::class,'getCities']);

Route::get('/customer/get-subcategories/{id}', [SellerCustomerController::class, 'getSubcategoriesAjax']);


// SELLER FINANCE

    
Route::get('/finance',[SellerFinanceController::class,'dashboard'])->name('financedashboard');

Route::get('/transactions',[SellerFinanceController::class,'transactions'])->name('finance.transactions');

Route::post('/transactions/store', [SellerFinanceController::class, 'store'])
        ->name('transactions.store');
        
Route::post('/transactions/dispute/{id}', [SellerFinanceController::class,'dispute'])
    ->name('transactions.dispute');

 Route::get('/invoice/{id}', 
        [SellerFinanceController::class, 'downloadInvoice']
    )->name('invoice.download');

        
Route::get('/transactions/dispute-view/{id}', 
    [SellerFinanceController::class, 'viewDispute']
)->name('transactions.dispute.view');

Route::post('/transactions/undispute', 
    [SellerFinanceController::class, 'undispute']
)->name('transactions.undispute');

Route::get('/transactions/export',
    [SellerFinanceController::class,'transactionexport']
)->name('transactions.export');


   Route::post('/withdraw', [SellerFinanceController::class,'withdraw'])
        ->name('finance.withdraw');

    Route::post('/payout/settings', [SellerFinanceController::class,'savePayoutSettings'])
        ->name('payout.settings');

    Route::post('/finance/bank/save', [SellerFinanceController::class,'saveBankDetails'])
        ->name('bank.save_account');
        
        // In web.php
Route::delete('/finance/bank/delete/{id}', [SellerFinanceController::class, 'deleteBankAccount'])->name('bank.delete_account');

Route::post('/withdraw',[SellerFinanceController::class,'withdraw'])->name('finance.withdraw');
Route::get('/payouts',[SellerFinanceController::class,'payouts'])->name('finance.payouts');
Route::get('/invoices',[SellerFinanceController::class,'invoices'])->name('finance.invoices');
Route::post('/generate-invoice',[SellerFinanceController::class,'generateInvoice'])->name('finance.generate.invoice');


Route::get('/commission',[SellerFinanceController::class,'commission'])->name('finance.commission');

Route::get('/disputes',[SellerFinanceController::class,'disputes'])->name('finance.disputes');

Route::post('/transactions/dispute', [SellerFinanceController::class, 'dispute'])
    ->name('transactions.dispute');
    
    Route::get('/transactions/dispute/view/{id}', 
    [SellerFinanceController::class,'viewDispute'])
    ->name('transactions.dispute.view');

Route::post('/raise-dispute',[SellerFinanceController::class,'raiseDispute'])->name('finance.raise.dispute');

Route::get('/support-chat',[SellerFinanceController::class,'supportChat'])->name('finance.support.chat');

Route::post('/send-message',[SellerFinanceController::class,'sendMessage'])->name('finance.send.message');

Route::get('/gst-report',[SellerFinanceController::class,'gstReport'])->name('finance.gst');

Route::get('/monthly-report',[SellerFinanceController::class,'monthlyReport'])->name('finance.monthly');

Route::get('/analytics',[SellerFinanceController::class,'analytics'])->name('analytics');

 Route::get('/finance/wallet', [SellerFinanceController::class, 'wallet'])
        ->name('finance.wallet');
        
Route::get('/finance/export', [SellerFinanceController::class, 'exportMonthlyReport'])
    ->name('finance.export');

Route::post('/wallet/settlement/update',
    [SellerFinanceController::class, 'updateSettlementCycle'])
    ->name('wallet.settlement.update');
    
    
    Route::get('/gst/export', [SellerFinanceController::class, 'exportGST'])
    ->name('gst.export');
    
    Route::get('/payout/success/{id}', 
    [SellerFinanceController::class,'markPayoutSuccess']
)->name('payout.success');

// Route::get('/seller/transactions/export', [SellerFinanceController::class, 'export'])
//     ->name('transactions.export');


Route::prefix('analytics')->group(function(){

    Route::get('/sales',[SellerFinanceController::class,'salesAnalytics'])
        ->name('analytics.sales');

    Route::get('/funnel',[SellerFinanceController::class,'funnelAnalytics'])
        ->name('analytics.funnel');

    Route::get('/customers',[SellerFinanceController::class,'customerAnalytics'])
        ->name('analytics.customers');

    Route::get('/campaigns',[SellerFinanceController::class,'campaignAnalytics'])
        ->name('analytics.campaigns');

    Route::get('/lead',[SellerFinanceController::class,'leadAnalytics'])
        ->name('analytics.lead');

    Route::get('/referral',[SellerFinanceController::class,'referralAnalytics'])
        ->name('analytics.referral');

});


Route::prefix('grievance')->group(function(){

    Route::get('/',[SellerFinanceController::class,'grievanceList'])
        ->name('grievance.list');

    Route::get('/create',[SellerFinanceController::class,'grievanceCreate'])
        ->name('grievance.create');

    Route::post('/store',[SellerFinanceController::class,'grievanceStore'])
        ->name('grievance.store');

    Route::get('/view/{id}',[SellerFinanceController::class,'grievanceView'])
        ->name('grievance.view');

    Route::post('/send-message',[SellerFinanceController::class,'grievanceMessage'])
        ->name('grievance.message');
        
        
        Route::post('/grievance/close', 
    [SellerFinanceController::class, 'closeGrievance']
)->name('grievance.close');

});

        
    });

});


Route::middleware('auth:customer')
->prefix('vtravel')
->group(function () {

 
    // Route::get('/vtravelproduct', [TravelProductController::class, 'index'])->name('vtravelproduct.index');

    // Route::get('/products/{id}/details', [TravelProductController::class, 'show'])->name('products.show');

    Route::resource('vtravelorders', VtravelOrderController::class);

    Route::get('/orderdetails', [VtravelOrderController::class, 'orderdetails'])->name('orderdetails');

    Route::get('/payment', [VtravelOrderController::class, 'payment'])->name('vtravel.payment.index');

    Route::get('/addaddresss', [VtravelAddressController::class, 'add_address'])->name('vtravel.addaddresss');

    Route::get('/reviews', [VtravelReviewController::class, 'reviews'])->name('reviews.index');

    Route::get('/myprofile', [CustomerProfileController::class, 'index'])->name('customer.profile');
    
    

    Route::post('/profile/update', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
    
    
    Route::get('/product/{id}/reviews', [VtravelReviewController::class, 'reviews'])->name('vtravel.product.reviews');
    Route::post('/review/store',[VtravelReviewController::class,'store'])
->name('vtravel.review.store');
    
    
    
    
   
// Address List Page (Select Delivery & Billing Address)
Route::get('/address', [VtravelAddressController::class, 'index'])
    ->name('vtravel.address.index');

// Show Create Address Form
Route::get('/address/create', [VtravelAddressController::class, 'create'])
    ->name('vtravel.address.create');

// Store New Address
Route::post('/address', [VtravelAddressController::class, 'store'])
    ->name('vtravel.address.store');

// Show Edit Address Form
Route::get('/address/{id}/edit', [VtravelAddressController::class, 'edit'])
    ->name('vtravel.address.edit');

// Update Address
Route::put('/address/{id}', [VtravelAddressController::class, 'update'])
    ->name('vtravel.address.update');

Route::patch('/address/{id}', [VtravelAddressController::class, 'update'])
    ->name('vtravel.address.update');

// Delete Address
Route::delete('/address/{id}', [VtravelAddressController::class, 'destroy'])
    ->name('vtravel.address.destroy');

// Set Default Address
Route::post('/address/{id}/set-default', [VtravelAddressController::class, 'setDefault'])
    ->name('vtravel.address.set-default');

// Place Order After Address Selection
Route::post('/address/place-order', [VtravelAddressController::class, 'placeOrder'])
    ->name('vtravel.address.place-order');

// Select Address (AJAX)
Route::post('/address/select', [VtravelAddressController::class, 'select'])
    ->name('vtravel.address.select');


    Route::get('/vtravelcart', [VtravelCartController::class, 'index'])->name('vtravel.cart.index');

    Route::post('/cart/add', [VtravelCartController::class, 'add'])->name('vtravel.cart.add');

    Route::post('/cart/update-quantity', [VtravelCartController::class, 'updateQty'])->name('vtravel.cart.updateQty');

    Route::post('/cart/remove', [VtravelCartController::class, 'remove'])->name('vtravel.cart.remove');
       Route::post('/cart/update-size', [VtravelCartController::class, 'updateSize'])->name('vtravel.cart.updateSize');

    Route::post('/cart/clear', [VtravelCartController::class, 'clear'])->name('vtravel.cart.clear');

    Route::get('/cart/count', [VtravelCartController::class, 'getCartCount'])->name('vtravel.cart.count');
      Route::post('/cart/wishlist', [VtravelCartController::class, 'moveToWishlist'])
    ->name('vtravel.cart.moveToWishlist');
    
    
    
    // ========== WISHLIST ROUTES USING VtravelWishlistController ==========
Route::prefix('wishlist')->name('vtravel.wishlist.')->controller(VtravelWishlistController::class)->group(function () {

    Route::get('/', 'index')->name('index');                     // View wishlist page

    Route::post('/toggle', 'toggle')->name('toggle');            // Toggle wishlist item

    Route::get('/items', 'getItems')->name('items');             // Get wishlist items (AJAX)

    Route::post('/add', 'add')->name('add');                     // Add to wishlist

    Route::delete('/remove/{id}', 'remove')->name('remove');     // Remove from wishlist

    Route::post('/move-to-cart/{id}', 'moveToCart')->name('move-to-cart'); // Move to cart

    Route::post('/clear', 'clear')->name('clear');               // Clear wishlist

    Route::get('/count', 'getCount')->name('count');             // Get wishlist count
});


//order data

 Route::get('/orders', [VtravelOrderController::class, 'orderList'])
        ->name('vtravel.order.list');
        

Route::post('/order/place', [VtravelOrderController::class, 'placeOrder'])
    ->name('vtravel.order.place');
    
    
           
Route::get('/order/confirmation/{id}', [VtravelOrderController::class, 'confirmation'])
    ->name('order.confirmation');
    

    // Order details
    Route::get('/orders/{id}', [VtravelOrderController::class, 'orderDetails'])
        ->name('vtravel.order.details');

});
    
    
  //vshop  
    

   Route::get('/productmanage', [VshopProductController::class, 'index'])->name('productmanage.index');
    Route::get('/productmanage/search/autocomplete', [VshopProductController::class, 'autocomplete'])->name('productmanage.autocomplete');
    // Show single product
    Route::get('/productmanage/{id}', [VshopProductController::class, 'show'])->name('productmanage.show');

    Route::get('/p/{id}', function ($id, \Illuminate\Http\Request $request) {
        if ($request->has('ref')) {
            $ref = $request->query('ref');
            session(['referral_code' => $ref]);
            cookie()->queue('referral_code', $ref, 60 * 24 * 30);
        }
        return redirect()->route('productmanage.show', $id);
    });

    Route::post('/vshop/apply-referral', [VshopOrderController::class, 'applyReferral'])->name('vshop.referral.apply');
    Route::post('/vshop/remove-referral', [VshopOrderController::class, 'removeReferral'])->name('vshop.referral.remove');
    
    // Enquiry store for product page

  Route::get('/vshoplogin', [EmployeeLoginController::class, 'showVshopLogin'])->name('vshoplogin');
Route::post('/vshoplogin', [EmployeeLoginController::class, 'vshopLogin'])->name('vshop.customer.login');

Route::get('/vshopregister', [EmployeeLoginController::class, 'showVshopRegister'])->name('vshop.customer.register.form');
Route::post('/vshopregister', [EmployeeLoginController::class, 'vshopRegister'])->name('vshop.customer.register');

Route::post('/vshop/customer-enquiry', [CustomerEnquiryController::class, 'store'])->name('vshop.enquiry.store');

  Route::post('/vshop/complaint/store', [CustomerGrievanceController::class, 'store'])
        ->name('vshop.complaint.store');
        
 Route::get('/vshop/complaint/details', [CustomerGrievanceController::class, 'details'])->name('vshop.complaint.details');

Route::get('/vshop/forgot-password', [App\Http\Controllers\Frontend\EmployeeLoginController::class, 'showForgotForm'])->name('vshop.forgot.form');
Route::post('/vshop/forgot-password', [App\Http\Controllers\Frontend\EmployeeLoginController::class, 'sendResetOtp'])->name('vshop.forgot.send');
Route::post('/vshop/verify-reset-otp', [App\Http\Controllers\Frontend\EmployeeLoginController::class, 'verifyResetOtp'])->name('vshop.forgot.verify');
Route::get('/vshop/reset-password', [App\Http\Controllers\Frontend\EmployeeLoginController::class, 'showResetForm'])->name('vshop.reset.form');
Route::post('/vshop/reset-password', [App\Http\Controllers\Frontend\EmployeeLoginController::class, 'resetPassword'])->name('vshop.reset.update');

Route::post('/vshoplogout', [EmployeeLoginController::class, 'logout'])->name('vshop.logout');


Route::middleware('auth:customer')->prefix('vshop')->name('vshop.')->group(function () {
  
    Route::get('/productmanage/create', [VshopProductController::class, 'create'])->name('productmanage.create');
    
 
    
    // Store new product
    Route::post('/productmanage', [VshopProductController::class, 'store'])->name('productmanage.store');
    // Show edit form
    Route::get('/productmanage/{id}/edit', [VshopProductController::class, 'edit'])->name('productmanage.edit');
    
    // Update product
    Route::put('/productmanage/{id}', [VshopProductController::class, 'update'])->name('productmanage.update');
    Route::patch('/productmanage/{id}', [VshopProductController::class, 'update'])->name('productmanage.update');
    
    // Delete product
    Route::delete('/productmanage/{id}', [VshopProductController::class, 'destroy'])->name('productmanage.destroy');
    
    Route::get('/products/{id}/details', [VshopProductController::class, 'show'])->name('products.show');
    
    
    
    // Update product status
    Route::post('/productmanage/{id}/status', [VshopProductController::class, 'updateStatus'])->name('productmanage.status');
    
    // Bulk delete
    Route::post('/productmanage/bulk-delete', [VshopProductController::class, 'bulkDelete'])->name('productmanage.bulk-delete');
    
    // Export products
    Route::get('/productmanage/export/csv', [VshopProductController::class, 'export'])->name('productmanage.export');
    
    // Search products
    Route::get('/productmanage/search/ajax', [VshopProductController::class, 'search'])->name('productmanage.search');
    
    
   
    

    
    // ========== CART ROUTES ==========
    // Cart routes using VshopCartController
    Route::get('/cart', [VshopCartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [VshopCartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update-quantity', [VshopCartController::class, 'updateQty'])->name('cart.updateQty');
    Route::post('/cart/update-size', [VshopCartController::class, 'updateSize'])->name('cart.updateSize');
    Route::post('/cart/remove', [VshopCartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/move-to-wishlist', [VshopCartController::class, 'moveToWishlist'])->name('cart.moveToWishlist');
    Route::post('/cart/apply-coupon', [VshopCartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::post('/cart/clear', [VshopCartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [VshopCartController::class, 'getCartCount'])->name('vshop.cart.count');
    Route::post('/cart/sync-after-login', [VshopCartController::class, 'syncCartAfterLogin'])->name('cart.sync');
    
    
     // ========== WISHLIST ROUTES USING VshopWishlistController ==========
    Route::prefix('wishlist')->name('wishlist.')->controller(VshopWishlistController::class)->group(function () {
        Route::get('/', 'index')->name('index');                    // View wishlist page
        Route::post('/toggle', 'toggle')->name('toggle');          // Toggle wishlist item
        Route::get('/items', 'getItems')->name('items');           // Get all wishlist items (AJAX)
        Route::post('/add', 'add')->name('add');                   // Add to wishlist
        Route::delete('/remove/{id}', 'remove')->name('remove');   // Remove from wishlist
        Route::post('/move-to-cart/{id}', 'moveToCart')->name('move-to-cart'); // Move to cart
        Route::post('/clear', 'clear')->name('clear');             // Clear wishlist
        Route::get('/count', 'getCount')->name('count');           // Get wishlist count
    });
    
    // ========== ADDRESS ROUTES (ADDED HERE) ==========
    // Address List Page (Select Delivery & Billing Address)
    Route::get('/vshopaddress', [VshopAddressController::class, 'index'])->name('address.index');
    
    // Show Create Address Form
    Route::get('/address/create', [VshopAddressController::class, 'create'])->name('address.create');
    
    // Store New Address
    Route::post('/address', [VshopAddressController::class, 'store'])->name('address.store');
    
    // Show Edit Address Form
    Route::get('/address/{id}/edit', [VshopAddressController::class, 'edit'])->name('address.edit');
    
    // Update Address
    Route::put('/address/{id}', [VshopAddressController::class, 'update'])->name('address.update');
    Route::patch('/address/{id}', [VshopAddressController::class, 'update'])->name('address.update');
    
    // Delete Address
   Route::delete('/address/{id}', [VshopAddressController::class, 'destroy'])
    ->name('address.delete');
    
    // Set Default Address
    Route::post('/address/{id}/set-default', [VshopAddressController::class, 'setDefault'])->name('address.set-default');
    
    // Place Order After Address Selection
    Route::post('/address/place-order', [VshopAddressController::class, 'placeOrder'])->name('address.place-order');
     Route::post('/address/select', [VshopAddressController::class, 'select'])
        ->name('address.select');

    // ========== OTHER VSHOP ROUTES ==========
    Route::get('/payment', [VshopOrderController::class, 'payment'])
        ->name('payment.index')
        ->middleware('auth:customer');

    Route::post('/coupon/apply', [VshopOrderController::class, 'applyCoupon'])
        ->name('coupon.apply')
        ->middleware('auth:customer');

    Route::post('/coupon/remove', [VshopOrderController::class, 'removeCoupon'])
        ->name('coupon.remove')
        ->middleware('auth:customer');

    Route::post('/payment/razorpay/create-order', [VshopOrderController::class, 'createRazorpayOrder'])
        ->name('payment.razorpay.create-order')
        ->middleware('auth:customer');

    Route::post('/rewards/apply', [VshopOrderController::class, 'applyRewards'])
        ->name('rewards.apply')
        ->middleware('auth:customer');

    Route::post('/rewards/remove', [VshopOrderController::class, 'removeRewards'])
        ->name('rewards.remove')
        ->middleware('auth:customer');
    Route::post('/checkout/address', [VshopOrderController::class, 'storeAddress'])
    ->name('checkout.address')
    ->middleware('auth:customer');


    Route::post('/order/place', [VshopOrderController::class, 'placeOrder'])
    ->name('order.place')
    ->middleware('auth:customer');
    
       
    Route::get('/order/confirmation/{id}', [VshopOrderController::class, 'confirmation'])
    ->name('order.confirmation')
    ->middleware('auth:customer');

    Route::get('/orders', [VshopOrderController::class, 'orderList'])
        ->name('order.list');

    // Order details
    Route::get('/orders/{id}', [VshopOrderController::class, 'orderDetails'])
        ->name('order.details');
        
    Route::post('/order-action', [VshopOrderController::class, 'orderAction'])->name('order.action');
    Route::get('/orders/{id}/invoice', [VshopOrderController::class, 'downloadInvoice'])->name('order.invoice');
    
    // Proxy route for meeting link
    Route::get('/meeting/{itemId}', [VshopOrderController::class, 'meetingProxy'])->name('meeting.proxy');
    
    Route::post('/update-address-session', [VshopAddressController::class, 'updateAddressSession'])
    ->name('update.address.session');

 
    Route::get('/addaddresss', [VshopAddressController::class, 'add_address'])->name('addaddresss');
    // Route::get('/reviews', [VshopReviewController::class, 'reviews'])->name('reviews.index');
    Route::get('/myprofile', [ProfileController::class, 'edit'])->name('customer.profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('customer.profile.update');
    
    Route::get('/vshop/product/{id}/reviews', [VshopRatingController::class, 'index'])->name('product.reviews');
    Route::get('/myaccount', [MyAccountController::class, 'index'])->name('myaccount');
    
    // Enquiry chat (placeholder - adjust to your chat system)
Route::get('/enquiry/chat', function() {
    return view('vshop.chat');
})->name('myaccount.chat');

Route::post('/enquiry/close', [App\Http\Controllers\Shop\MyAccountController::class, 'closeEnquiry'])->name('myaccount.close-enquiry');
Route::post('/enquiry/upload-attachment', [App\Http\Controllers\Shop\MyAccountController::class, 'uploadEnquiryAttachment'])->name('myaccount.upload-attachment');

// Close enquiry
Route::post('/enquiry/close', [App\Http\Controllers\Shop\MyAccountController::class, 'closeEnquiry'])->name('myaccount.close-enquiry');

Route::post('/myaccount/update-profile', [MyAccountController::class, 'updateProfile'])->name('myaccount.update-profile');
Route::post('/myaccount/add-address', [MyAccountController::class, 'addAddress'])->name('myaccount.add-address');
Route::post('/myaccount/update-address/{id}', [MyAccountController::class, 'updateAddress'])->name('myaccount.update-address');
Route::delete('/myaccount/delete-address/{id}', [MyAccountController::class, 'deleteAddress'])->name('myaccount.delete-address');
Route::post('/myaccount/recharge-wallet', [MyAccountController::class, 'rechargeWallet'])->name('myaccount.recharge-wallet');
Route::post('/myaccount/recharge-wallet/create-order', [MyAccountController::class, 'createRechargeOrder'])->name('myaccount.recharge-wallet.create-order');
Route::post('/myaccount/recharge-wallet/verify', [MyAccountController::class, 'verifyRecharge'])->name('myaccount.recharge-wallet.verify');
Route::post('/myaccount/add-gift-card', [MyAccountController::class, 'addGiftCard'])->name('myaccount.add-gift-card');
Route::post('/myaccount/redeem-reward', [MyAccountController::class, 'redeemReward'])->name('myaccount.redeem-reward');
Route::post('/campaign/participate/{slug}', [MyAccountController::class, 'participateCampaign'])->name('campaign.participate');
Route::post('/myaccount/toggle-auto-renew', [MyAccountController::class, 'toggleAutoRenew'])->name('myaccount.toggle-auto-renew');
Route::delete('/myaccount/wishlist/{id}', [MyAccountController::class, 'removeFromWishlist'])->name('myaccount.remove-wishlist');
Route::post('/myaccount/move-to-cart/{id}', [MyAccountController::class, 'moveToCart'])->name('myaccount.move-to-cart');
Route::post('/myaccount/generate-referral-link', [MyAccountController::class, 'generateReferralLink'])->name('myaccount.generate-referral-link');
Route::post('/myaccount/change-password', [MyAccountController::class, 'changePassword'])->name('myaccount.change-password');
Route::post('/myaccount/logout-all-devices', [MyAccountController::class, 'logoutAllDevices'])->name('myaccount.logout-all-devices');
Route::post('/myaccount/logout-session', [MyAccountController::class, 'logoutSession'])->name('myaccount.logout-session'); // <-- ADD THIS
Route::delete('/myaccount/delete-account', [MyAccountController::class, 'deleteAccount'])->name('myaccount.delete-account');
Route::post('/myaccount/buy-again/{orderId}', [MyAccountController::class, 'buyAgain'])->name('myaccount.buy-again');
Route::post('/myaccount/update-settings', [MyAccountController::class, 'updateSettings'])->name('myaccount.update-settings');


// Profile & Other Info
Route::post('/myaccount/update-profile', [MyAccountController::class, 'updateProfile'])->name('myaccount.update-profile');
Route::post('/myaccount/update-other-info', [MyAccountController::class, 'updateOtherInfo'])->name('myaccount.update-other-info');
Route::post('/myaccount/update-bank-details', [MyAccountController::class, 'updateBankDetails'])->name('myaccount.update-bank-details');

// Addresses
Route::post('/myaccount/add-address', [MyAccountController::class, 'addAddress'])->name('myaccount.add-address');
Route::post('/myaccount/update-address/{id}', [MyAccountController::class, 'updateAddress'])->name('myaccount.update-address');
Route::delete('/myaccount/delete-address/{id}', [MyAccountController::class, 'deleteAddress'])->name('myaccount.delete-address');


});
// OTP Verification Route (common)
Route::post('/verify-otp', [EmployeeLoginController::class, 'verifyRegisterOtp'])->name('verify.otp');

// Resend OTP Route (for registration)
Route::post('/resend-otp', [EmployeeLoginController::class, 'resendOtp'])->name('resend.otp');

Route::prefix('vshop')->name('vshop.')->group(function () {
    Route::get('/store/{slug}', [StoreController::class, 'show'])
        ->name('store.show');
});

Route::prefix('vtravel')->name('vtravel.')->group(function () {
    Route::get('/myaccount', [MyAccountController::class, 'index'])->name('vtravel.myaccount');

    Route::get('/store/{slug}', [StoreController::class, 'show'])
        ->name('store.show');
});

require __DIR__ . '/auth.php';


Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])
    ->name('admin.login');

Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('admin.login.store');
Route::post('/admin/login/verify-otp', [AuthenticatedSessionController::class, 'verifyStaffOtp'])->name('admin.login.verify-otp');

Route::get('/admin/2fa', [AuthenticatedSessionController::class, 'show2faForm'])
    ->name('admin.2fa');
Route::post('/admin/2fa', [AuthenticatedSessionController::class, 'verify2fa'])
    ->name('admin.2fa.verify');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
// ==================== ADMIN PROTECTED ROUTES ====================
Route::prefix('admin')->name('admin.')->middleware('auth:web,admin_staff')->group(function () {
    
    Route::get('/switch-panel/{type}', [AuthenticatedSessionController::class, 'switchPanel'])->name('switch.panel');
    Route::get('/global-search', [App\Http\Controllers\admin\GlobalSearchController::class, 'search'])->name('global.search');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
       // Banner List
    Route::get('banners', [PageController::class, 'bannerlist'])
        ->name('banners.index');

    // Create Page
    Route::get('banners/create', [PageController::class, 'bannercreate'])
        ->name('banners.create');

    // Store (Multiple Upload)
    Route::post('banners/store', [PageController::class, 'bannerstore'])
        ->name('banners.store');

    // Delete
    Route::get('banners/delete/{id}', [PageController::class, 'bannerdestroy'])
        ->name('banners.delete');
        
    Route::get('banners/edit/{id}', [PageController::class,'banneredit'])->name('banners.edit');
    Route::post('banners/update/{id}', [PageController::class,'bannerupdate'])->name('banners.update');
    

//advertisements    

Route::get('/advertisements', [AdvertisementController::class, 'index'])->name('ads.index');
Route::post('/advertisements/store', [AdvertisementController::class, 'store'])->name('ads.store');
Route::get('/advertisements/create', [AdvertisementController::class, 'create'])->name('ads.create');
Route::get('/advertisements/edit/{id}', [AdvertisementController::class, 'edit'])->name('ads.edit');
Route::match(['post', 'put'], '/advertisements/update/{id}', [AdvertisementController::class, 'update'])->name('ads.update');
Route::delete('/advertisements/delete/{id}', [AdvertisementController::class, 'destroy'])->name('ads.delete');
Route::delete('/advertisements/media/{id}', [AdvertisementController::class, 'deleteMedia'])->name('ads.delete.media');
Route::post('/advertisements/toggle-status', [AdvertisementController::class, 'toggleStatus'])->name('ads.toggle-status');
Route::get('/advertisements/{id}/participants', [AdvertisementController::class, 'getCampaignParticipants'])->name('ads.participants');

//dynamic page creation
Route::get('/pages', [PageController::class,'index'])->name('pages.index');
Route::get('/pages/create', [PageController::class,'create'])->name('pages.create');
Route::post('/pages/store', [PageController::class,'store'])->name('pages.store');
Route::get('/pages/edit/{id}', [PageController::class,'edit'])->name('pages.edit');
Route::post('/pages/update/{id}', [PageController::class,'update'])->name('pages.update');
Route::delete('/pages/delete/{id}', [PageController::class,'destroy'])->name('pages.delete');
Route::post('/pages/submit-review/{id}', [PageController::class, 'submitForReview'])->name('pages.submit-review');
Route::post('/pages/approve/{id}', [PageController::class, 'approvePage'])->name('pages.approve');
Route::post('/pages/reject/{id}', [PageController::class, 'rejectPage'])->name('pages.reject');
Route::post('/pages/publish/{id}', [PageController::class, 'publishPage'])->name('pages.publish');
Route::delete('/pages/image/delete/{id}', [PageController::class, 'deleteImage'])->name('pages.image.delete');

//smart_auto_questions

Route::get('/smart-questions', [SmartQuestionController::class,'index'])->name('smart.index');
Route::get('/smart-questions/create', [SmartQuestionController::class,'create'])->name('smart.create');
Route::post('/smart-questions/store', [SmartQuestionController::class,'store'])->name('smart.store');
Route::get('/smart-questions/edit/{id}', [SmartQuestionController::class,'edit'])->name('smart.edit');
Route::post('/smart-questions/update/{id}', [SmartQuestionController::class,'update'])->name('smart.update');
Route::delete('/smart-questions/delete/{id}', [SmartQuestionController::class,'destroy'])->name('smart.delete');

//seller approval
Route::post('/seller/approve', [ReportController::class, 'approve'])->name('seller.approve');
Route::post('/seller/reject', [ReportController::class, 'reject'])->name('seller.reject');
    
Route::get('/website-templates',[WebsiteTemplateController::class,'index'])->name('website.templates.index');

Route::get('/website-templates/create',[WebsiteTemplateController::class,'create'])->name('website.templates.create');

Route::post('/website-templates/store',[WebsiteTemplateController::class,'store'])->name('website.templates.store');

Route::get('admin/website/templates/{id}/edit',[WebsiteTemplateController::class,'edit'])->name('website.templates.edit');

Route::put(
    'admin/website/templates/{id}',
    [WebsiteTemplateController::class,'update']
)->name('website.templates.update');

Route::delete(
    'admin/website/templates/{id}',
    [WebsiteTemplateController::class,'destroy']
)->name('website.templates.destroy');



     Route::get('/approval/products', [ApprovalController::class, 'products'])
        ->name('approval.products');
        
            Route::get('approval/verify/{id}', [ApprovalController::class, 'verify'])->name('approval.verify');
    Route::post('approval/submit-verification/{id}', [ApprovalController::class, 'submitVerification'])->name('approval.submit.verification');
    Route::post('approval/update', [ApprovalController::class, 'updateStatus'])->name('approval.update');
    Route::post('approval/change-status/{id}', [ApprovalController::class, 'changeStatus'])->name('approval.change.status');
    Route::post('approval/force-disable/{id}', [ApprovalController::class, 'forceDisable'])->name('approval.force.disable');
    
    // Version History Routes
    Route::get('approval/version-history/{id}', [ApprovalController::class, 'getVersionHistory'])->name('approval.version.history');
    Route::post('approval/rollback/{id}', [ApprovalController::class, 'rollbackVersion'])->name('approval.rollback');
    Route::get('approval/activity-logs/{id}', [ApprovalController::class, 'getActivityLogs'])->name('approval.activity.logs');
    
    // Bulk Actions
    Route::post('approval/bulk-action', [ApprovalController::class, 'bulkApprove'])->name('approval.bulk.action');
    
    // Dropshipping Routes
    Route::get('approval/suppliers', [ApprovalController::class, 'getSuppliers'])->name('admin.approval.suppliers');
    Route::post('approval/approve-supplier/{id}', [ApprovalController::class, 'approveSupplier'])->name('admin.approval.approve.supplier');
    Route::post('approval/sync-product-mapping', [ApprovalController::class, 'syncProductMapping'])->name('admin.approval.sync.mapping');
    
    // Review Moderation
    Route::get('approval/reviews', [ApprovalController::class, 'moderateReviews'])->name('admin.approval.reviews');
    Route::post('approval/review-removal/{id}', [ApprovalController::class, 'approveReviewRemoval'])->name('admin.approval.review.removal');

    Route::get('/approval/categories', [ApprovalController::class, 'categories'])
        ->name('approval.categories');

    Route::get('/approval/attributes', [ApprovalController::class, 'attributes'])
        ->name('approval.attributes');

    Route::post('/approval/update-status', [ApprovalController::class, 'updateStatus'])
        ->name('approval.update');


    Route::get('/', [AdminReviewController::class, 'index'])->name('index');
    Route::get('/{review}/edit', [AdminReviewController::class, 'edit'])->name('edit')->where('review', '[0-9]+');
    Route::put('/{review}', [AdminReviewController::class, 'update'])->name('update')->where('review', '[0-9]+');
    Route::delete('/{review}', [AdminReviewController::class, 'destroy'])->name('destroy')->where('review', '[0-9]+');
    Route::post('/{review}/toggle-status', [AdminReviewController::class, 'toggleStatus'])->name('toggle-status')->where('review', '[0-9]+');
    Route::post('/{review}/reply', [AdminReviewController::class, 'addReply'])->name('reply')->where('review', '[0-9]+');
    Route::get('/stats', [AdminReviewController::class, 'getStats'])->name('stats');
    Route::post('/reviews/{id}/approve',[AdminReviewController::class,'approve'])->name('review.approve');
    Route::post('/reviews/{id}/reject',[AdminReviewController::class,'reject'])->name('review.reject');
    Route::post('/reviews/{id}/block-seller',[AdminReviewController::class,'blockSeller'])->name('review.block_seller');
    Route::post('/reviews/{id}/inactive-seller',[AdminReviewController::class,'inactiveSeller'])->name('review.inactive_seller');

    // Logout Route
    Route::post('/adminlogout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::resource('products', ProductController::class);
    Route::get('/products/{id}/combo', [ProductController::class, 'comboPage'])->name('products.combo');

    Route::get('/admin/products/search', [ProductController::class, 'search'])->name('products.search');
    // Combo Products Routes
    Route::get('/admin/products/{id}/combo', [ProductController::class, 'comboPage'])->name('products.combo');
    Route::post('/admin/products/{id}/save-combo', [ProductController::class, 'saveCombo'])->name('products.save-combo');
    Route::get('/admin/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/combos', [ProductController::class, 'combosIndex'])->name('combos.index');
    Route::delete('/admin/combos/{id}', [ProductController::class, 'comboDestroy'])->name('combos.destroy');

// Clients/Locations Management (uses type checking in controller)
    Route::resource('clients', ClientController::class);
    
    // Search route for both
    Route::post('clients/search', [ClientController::class, 'search'])->name('clients.search');
    
    // Location routes (for travel admin - will be filtered by controller)
    Route::get('location', [ClientController::class, 'index'])->name('location.index');
    Route::get('location/create', [ClientController::class, 'create'])->name('location.create');
    Route::post('location', [ClientController::class, 'store'])->name('location.store');
    Route::get('location/{id}/edit', [ClientController::class, 'edit'])->name('location.edit');
    Route::put('location/{id}', [ClientController::class, 'update'])->name('location.update');
    Route::delete('location/{id}', [ClientController::class, 'destroy'])->name('location.destroy');

      Route::get('location/search', [ClientController::class, 'search'])
         ->name('location.search');
    
    // Brand-Location routes
    Route::get('brand-location/create', [ClientController::class, 'branchcreate'])->name('brand-location.create');
    Route::post('branchstore', [ClientController::class, 'brachstore'])->name('brand-location.store');
    
    // Brand Products
    Route::get('brandsproducts', [ClientController::class, 'brandProducts'])->name('brands.products');

       Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
    
    // Or if you have a different controller
    Route::get('/tours', [ProductController::class, 'index'])->name('tours.index');
    Route::post('/admin/categories/search', [\App\Http\Controllers\admin\CategoryController::class, 'search'])->name('categories.search');

    Route::resource('categories', \App\Http\Controllers\admin\CategoryController::class);
    Route::resource('subcategories', \App\Http\Controllers\admin\SubcategoryController::class);
    Route::resource('coupons', \App\Http\Controllers\admin\CouponController::class);
    Route::post('coupons/{id}/toggle-status', [\App\Http\Controllers\admin\CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
    
    // Marketing & Growth Routes
    Route::get('/referral-system', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'referralSystem'])->name('referrals.index');
    Route::get('/referral-history', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'referralHistory'])->name('admin.referrals.history');
    Route::get('/campaign-tracking', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'campaignTracking'])->name('campaigns.index');
    
    // Referral System API routes
    Route::group(['prefix' => 'referral-api'], function () {
        Route::get('/system-stats', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'getSystemStats']);
        Route::get('/top-referrers', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'getTopReferrers']);
        Route::get('/campaigns', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'getCampaigns']);
        Route::post('/campaigns', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'storeCampaign']);

        Route::get('/categories', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'getCategories']);
        Route::get('/products', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'getProducts']);
        Route::get('/sellers', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'getSellers']);
        Route::get('/category-rewards', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'categoryRewardsList']);
        Route::post('/category-rewards', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'storeCategoryReward']);
        Route::put('/category-rewards/{id}', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'updateCategoryReward']);
        Route::delete('/category-rewards/{id}', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'deleteCategoryReward']);
        
        Route::get('/referral-configurations', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'getReferralConfigurations']);
        Route::post('/referral-configurations/save', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'saveReferralConfigurations']);
        Route::put('/referral-configurations/{id}', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'updateReferralConfiguration']);
        Route::delete('/referral-configurations/{id}', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'deleteReferralConfiguration']);
        
        Route::get('/product-campaigns', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'productCampaignsList']);
        Route::post('/product-campaigns', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'storeProductCampaign']);
        Route::put('/product-campaigns/{id}', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'updateProductCampaign']);
        Route::delete('/product-campaigns/{id}', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'deleteProductCampaign']);
        
        Route::get('/levels', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'levelsList']);
        Route::post('/levels', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'storeLevel']);
        Route::put('/levels/{id}', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'updateLevel']);
        Route::delete('/levels/{id}', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'deleteLevel']);
        
        Route::get('/reward-tiers-list', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'rewardTiersList']);
        Route::post('/reward-tier', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'storeRewardTier']);
        Route::post('/reward-tier/{id}', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'updateRewardTier']);
        Route::post('/reward-tier/{id}/delete', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'deleteRewardTier']);
        
        Route::get('/transactions', [\App\Http\Controllers\admin\MarketingGrowthController::class, 'getTransactions']);
    });
    Route::get('/get-subcategories', [App\Http\Controllers\admin\ProductController::class, 'getSubcategories'])
        ->name('get.subcategories');

        Route::get('orders', [OrderController::class, 'index'])
        ->name('orders.index');
    // New routes for Order Management pages
    Route::get('orders/lifecycle', [OrderController::class, 'lifecycle'])
        ->name('admin.orders.lifecycle');
        
     // Update routes
    Route::put('/orders/{order}/update-payment', [OrderController::class, 'updatePayment'])->name('orders.update-payment');
    Route::put('/orders/{order}/update-dispatch', [OrderController::class, 'updateDispatch'])->name('orders.update-dispatch');
    Route::put('/orders/{order}/update-delivery', [OrderController::class, 'updateDelivery'])->name('orders.update-delivery');
    Route::put('/orders/{order}/close', [OrderController::class, 'closeOrder'])->name('orders.close');
    Route::get('orders/cancellation', [OrderController::class, 'cancellation'])
        ->name('admin.orders.cancellation');

      
      
      
      
    Route::get('/export-cancellation', [OrderController::class, 'exportCancellation'])->name('orders.export-cancellation');
    Route::get('orders/payment', [OrderController::class, 'payment'])
        ->name('admin.orders.payment');
    Route::get('orders/payments/{id}', [OrderController::class, 'getPaymentDetails'])->name('orders.payment-details');
    Route::get('orders/invoice/validate/{id}', [OrderController::class, 'validateInvoice'])->name('orders.invoice-validate');
    Route::post('orders/payments/{id}/suspicious', [OrderController::class, 'markSuspicious'])->name('orders.payment-suspicious');
    Route::post('orders/payments/{id}/fraud', [OrderController::class, 'flagFraud'])->name('orders.payment-fraud');
    Route::post('orders/payments/{id}/clear-fraud', [OrderController::class, 'clearFraudFlag'])->name('orders.payment-clear-fraud');
    Route::post('orders/payments/{id}/refund', [OrderController::class, 'refundPayment'])->name('orders.payment-refund');
    // Existing routes continue
    Route::get('orders/invoices', [OrderController::class, 'invoices'])
        ->name('orders.invoices');
    Route::get('orders/returns', [OrderController::class, 'returns'])
        ->name('orders.returns');
    // Removed duplicate erroneous order routes

    Route::get('orders/{id}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/{id}/manage', [OrderController::class, 'showManageOrder'])->name('orders.edit');
    Route::delete('orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');

    Route::post('orders/{id}/update-status', [OrderController::class, 'updateStatus'])
        ->name('orders.updateStatus');
    // Add this route for export
Route::get('/orders/export', [OrderController::class, 'exportOrders'])->name('orders.export');
    //attributes
    Route::get('attributes', [AttributeController::class, 'index'])->name('attributes.index');
    Route::post('attributes', [AttributeController::class, 'store'])->name('attributes.store');
    Route::delete('attributes/{id}', [AttributeController::class, 'destroy'])->name('attributes.destroy');

    Route::get('attributes/{id}/terms', [AttributeController::class, 'terms'])->name('attributes.terms');
    Route::post('attributes/{id}/terms', [AttributeController::class, 'termsStore'])->name('attributes.terms.store');
    Route::get('admin/attributes/{attribute}/terms/{term}/edit', [AttributeController::class, 'edit'])->name('attributes.terms.edit');
    Route::put('admin/attributes/{attribute}/terms/{term}', [AttributeController::class, 'update'])->name('attributes.terms.update');
    Route::delete('admin/attributes/{attribute}/terms/{term}', [AttributeController::class, 'termsdestroy'])->name('attributes.terms.destroy');

    Route::get('admin/products/{product}/attributes', [ProductController::class, 'attributes'])
        ->name('products.attributes');
    Route::post('admin/products/{product}/attributes', [ProductController::class, 'attributesStore'])
        ->name('products.attributes.store');
    Route::get('admin/products/{product}/attributes/{combinationKey}/edit', [ProductController::class, 'attributesEdit'])
        ->name('products.attributes.edit');
    Route::put('admin/products/{product}/attributes/{combinationKey}', [ProductController::class, 'attributesUpdate'])
        ->name('products.attributes.update');
    Route::delete('admin/products/attributes/{id}', [ProductController::class, 'attributesDestroy'])
        ->name('products.attributes.destroy');



    Route::get('/admin/customers', [\App\Http\Controllers\admin\CustomerController::class, 'index'])
        ->name('customers.index');
    Route::get('/customers/{id}', [\App\Http\Controllers\admin\CustomerController::class, 'show'])
        ->name('customers.show');
    Route::post('customers/search', [\App\Http\Controllers\admin\CustomerController::class, 'search'])->name('customers.search');

    Route::delete('/customers/{id}', [\App\Http\Controllers\admin\CustomerController::class, 'destroy'])
        ->name('customers.destroy');

    Route::post('customers/bulk-destroy', [\App\Http\Controllers\admin\CustomerController::class, 'bulkDestroy'])
        ->name('customers.bulk-destroy');
        
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/block-control', [\App\Http\Controllers\admin\CustomerController::class, 'blockControl'])->name('block-control');
        Route::post('/{id}/toggle-status', [\App\Http\Controllers\admin\CustomerController::class, 'toggleStatus'])->name('toggle-status');
        
        Route::get('/merge', [\App\Http\Controllers\admin\CustomerController::class, 'mergeView'])->name('merge');
        Route::post('/merge', [\App\Http\Controllers\admin\CustomerController::class, 'mergeSubmit'])->name('merge.submit');
        
        // Insights
        Route::get('/insights/high-value', [\App\Http\Controllers\admin\CustomerController::class, 'insightsHighValue'])->name('insights.high-value');
        Route::get('/insights/frequent-return', [\App\Http\Controllers\admin\CustomerController::class, 'insightsFrequentReturn'])->name('insights.frequent-return');
        Route::get('/insights/bad-reviews', [\App\Http\Controllers\admin\CustomerController::class, 'insightsBadReviews'])->name('insights.bad-reviews');
        
        // Data Validation
        Route::get('/validation/address', [\App\Http\Controllers\admin\CustomerController::class, 'validationAddress'])->name('validation.address');
        Route::get('/validation/duplicates', [\App\Http\Controllers\admin\CustomerController::class, 'validationDuplicates'])->name('validation.duplicates');
    });
    
    


    Route::resource('roles', \App\Http\Controllers\admin\RoleController::class);

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('payment-finance', [PaymentFinanceController::class, 'index'])->name('payment.finance');
    Route::get('payments/transactions', [PaymentFinanceController::class, 'transactions'])
        ->name('payments.transactions');
    Route::get('payments/taxes', [PaymentFinanceController::class, 'taxes'])->name('payments.taxes');
    Route::post('admin/taxes/store', [PaymentFinanceController::class, 'storeTax'])->name('taxes.store');

    Route::get('transactions/filter', [App\Http\Controllers\admin\PaymentFinanceController::class, 'filter'])
        ->name('transactions.filter');

    // Change Password Form
    Route::get('/admin/change-password', [App\Http\Controllers\admin\AuthController::class, 'changePassword'])
        ->name('change.password');
    Route::post('/admin/change-password', [App\Http\Controllers\admin\AuthController::class, 'updatePassword'])
        ->name('update.password');

    //refund section     
    Route::get('refund/requests', [RefundController::class, 'requests'])->name('refund.requests');
    Route::get('refund/reasons', [RefundController::class, 'reasons'])->name('refund.reasons');
    Route::get('refund/process', [RefundController::class, 'process'])->name('refund.process');
    Route::get('refund/settings', [RefundController::class, 'settings'])->name('refund.settings');
    
    Route::post('refunds/search', [RefundController::class, 'search'])->name('refunds.search');

    Route::get('refund/reasons/create', [RefundController::class, 'create'])->name('refund.reasons.create');
    Route::post('refund/reasons/store', [RefundController::class, 'store'])->name('refund.reasons.store');
    Route::get('refund/reasons/{id}/edit', [RefundController::class, 'edit'])->name('refund.reasons.edit');
    Route::put('refund/reasons/{id}', [RefundController::class, 'update'])->name('refund.reasons.update');
    Route::delete('refund/reasons/{id}', [RefundController::class, 'destroy'])->name('refund.reasons.destroy');

    Route::post('refund/process/store', [RefundController::class, 'storeProcess'])->name('refund.process.store');
    Route::get('refund/process/{id}/edit', [RefundController::class, 'editProcess'])->name('refund.process.edit');
    Route::put('refund/process/{id}', [RefundController::class, 'updateProcess'])->name('refund.process.update');
    Route::delete('refund/process/{id}', [RefundController::class, 'destroyProcess'])->name('refund.process.destroy');
    
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets/{id}/toggle-status', [AdminTicketController::class, 'toggleStatus'])->name('tickets.toggle-status');
    Route::get('/tickets/{id}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{id}/edit', [AdminTicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{id}', [AdminTicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{id}', [AdminTicketController::class, 'destroy'])->name('tickets.destroy');
    Route::get('admin/tickets/create', [AdminTicketController::class, 'create'])->name('tickets.create');
    Route::post('admin/tickets/store', [AdminTicketController::class, 'store'])->name('tickets.store');

    Route::get('admin/tickets/search-phone/{phone}', [AdminTicketController::class, 'searchPhone']);
    Route::get('/admin/tickets/get/{id}', [AdminTicketController::class, 'getTicket']);
    Route::post('/admin/tickets/reply', [AdminTicketController::class, 'reply'])->name('ticket.reply');

    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
    Route::get('/blogs/create', [BlogController::class, 'create'])->name('blogs.create');
    Route::post('/blogs', [BlogController::class, 'store'])->name('blogs.store');
    Route::get('/blogs/{id}/edit', [BlogController::class, 'edit'])->name('blogs.edit');
    Route::put('/blogs/{id}', [BlogController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy'])->name('blogs.destroy');
    Route::post('/blogs/{id}/toggle-featured', [BlogController::class, 'toggleFeatured'])->name('blogs.toggle-featured');
    Route::post('/blogs/{id}/update-status', [BlogController::class, 'updateStatus'])->name('blogs.update-status');
    Route::get('/blogs/{id}/comments', [BlogController::class, 'comments'])->name('blogs.comments');
    Route::post('/blogs/{id}/comments/{commentId}/update-status', [BlogController::class, 'updateCommentStatus'])->name('blogs.comments.update-status');
    Route::delete('/blogs/{id}/comments/{commentId}', [BlogController::class, 'deleteComment'])->name('blogs.comments.delete');
    
    Route::get('/blog-categories', [BlogCategoryController::class, 'index'])->name('blog-categories.index');
    Route::get('/blog-categories/create', [BlogCategoryController::class, 'create'])->name('blog-categories.create');
    Route::post('/blog-categories', [BlogCategoryController::class, 'store'])->name('blog-categories.store');
    Route::get('/blog-categories/{id}/edit', [BlogCategoryController::class, 'edit'])->name('blog-categories.edit');
    Route::put('/blog-categories/{id}', [BlogCategoryController::class, 'update'])->name('blog-categories.update');
    Route::delete('/blog-categories/{id}', [BlogCategoryController::class, 'destroy'])->name('blog-categories.destroy');
    Route::post('/blog-categories/{id}/toggle-status', [BlogCategoryController::class, 'toggleStatus'])->name('blog-categories.toggle-status');
    Route::post('/blog-categories/update-sort-order', [BlogCategoryController::class, 'updateSortOrder'])->name('blog-categories.update-sort-order');
    Route::post('/blog-categories/bulk-actions', [BlogCategoryController::class, 'bulkActions'])->name('blog-categories.bulk-actions');
    
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity.log');
    Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activitylog.show');
    Route::delete('/activity-logs/{id}', [ActivityLogController::class, 'destroy'])->name('activitylog.destroy');
    Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activitylog.export');
    Route::post('/activity-logs/clear-old', [ActivityLogController::class, 'clearOldLogs'])->name('activitylog.clear-old');
    Route::delete('admin/activity-logs/{id}', [ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
    
    
    
    
});



Route::prefix('admin')->name('admin.')->middleware('auth:web,admin_staff')->group(function () {
    // ... other admin routes ...
    
 
    // Add these routes INSIDE the admin group
    Route::post('orders/{order}/approve-request', [OrderController::class, 'approveRequest'])
        ->name('orders.approve-request');
    
    Route::post('orders/{order}/reject-request', [OrderController::class, 'rejectRequest'])
        ->name('orders.reject-request');
    
    Route::post('orders/{order}/process-refund', [OrderController::class, 'processRefund'])
        ->name('orders.process-refund');
    
    // Rest of your admin routes...
});

// Review Moderation Routes
Route::post('/admin/approval/review/{id}/status/{status}', [ApprovalController::class, 'markReviewStatus'])->name('approval.review.status');
Route::post('/admin/approval/review/{id}/removal/approve', [ApprovalController::class, 'approveReviewRemoval'])->name('approval.review.removal.approve');


// Admin Approval Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::prefix('approval')->name('approval.')->group(function () {
        // Main products page
        Route::get('/products', [ApprovalController::class, 'products'])->name('products');
        
        // Single item status update
        Route::post('/update', [ApprovalController::class, 'updateStatus'])->name('update');
        
        // Bulk actions
        Route::post('/bulk/action', [ApprovalController::class, 'bulkAction'])->name('bulk.action');
        Route::post('/bulk/category', [ApprovalController::class, 'bulkCategoryChange'])->name('bulk.category');
        
        // Verify route
        Route::get('/verify/{id}', [ApprovalController::class, 'verify'])->name('verify');
        Route::post('/verify/{id}', [ApprovalController::class, 'submitVerification'])->name('submit.verification');
        
        // Subcategories route
        Route::get('/subcategories/by-category', [ApprovalController::class, 'getSubcategoriesByCategory'])->name('subcategories.by-category');
    });
});


    
    // Add these routes inside your admin route group
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Existing routes...
    
    // Customer management routes
    Route::post('/customers/update-status', [App\Http\Controllers\admin\CustomerController::class, 'updateStatus'])->name('admin.customers.update-status');
    Route::post('/customers/update-type', [App\Http\Controllers\admin\CustomerController::class, 'updateType'])->name('admin.customers.update-type');
    
    // Resource route for customers (if not already defined)
    Route::resource('customers', App\Http\Controllers\admin\CustomerController::class);
    
     Route::get('/customers/duplicates', [App\Http\Controllers\admin\CustomerController::class, 'validationDuplicates'])->name('admin.customers.duplicates');
    Route::post('/customers/merge-verify', [App\Http\Controllers\admin\CustomerController::class, 'mergeVerify'])->name('admin.customers.merge.verify');
    Route::post('/customers/quick-merge', [App\Http\Controllers\admin\CustomerController::class, 'quickMerge'])->name('admin.customers.quick.merge');
    Route::get('/customers/duplicate-details', [App\Http\Controllers\admin\CustomerController::class, 'getDuplicateDetails'])->name('admin.customers.duplicate.details');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    
    // Finance Management Main Page
    Route::get('finance-management', [AdminFinanceController::class, 'index'])->name('admin.finance-management');
    Route::get('change-currency/{currency}', [AdminFinanceController::class, 'changeCurrency'])->name('admin.change-currency');
    Route::get('finance/get-products/{category_id}', [AdminFinanceController::class, 'getProductsByCategory'])->name('admin.finance.get-products');
    Route::post('finance/update-earnings-settings', [AdminFinanceController::class, 'updateEarningsSettings'])->name('admin.finance.update-earnings-settings');
    Route::post('finance/update-leads-boost-settings', [AdminFinanceController::class, 'updateLeadsBoostSettings'])->name('admin.finance.update-leads-boost-settings');

    Route::post('finance/update-tax-rules', [AdminFinanceController::class, 'updateTaxRules'])->name('admin.finance.update-tax-rules');
    Route::post('finance/update-dispute-rules', [AdminFinanceController::class, 'updateDisputeRules'])->name('admin.finance.update-dispute-rules');


// Add these routes to your finance routes group
  Route::post('admin/finance/update-global-rules', [AdminFinanceController::class, 'updateGlobalRules'])->name('admin.finance.update-global-rules');
Route::get('/finance/get-global-rules', [AdminFinanceController::class, 'getGlobalRules'])->name('admin.finance.get-global-rules');
Route::get('/finance/get-rule', [AdminFinanceController::class, 'getRule'])->name('admin.finance.get-rule');
Route::post('/finance/delete-rule', [AdminFinanceController::class, 'deleteRule'])->name('admin.finance.delete-rule');
    // ==========================================
    // COMMISSION ROUTES
    // ==========================================
    Route::post('finance/update-commission', [AdminFinanceController::class, 'updateCommission'])->name('admin.finance.update-commission');
  
    
    // ==========================================
    // PAYOUT ROUTES
    // ==========================================
    Route::post('finance/approve-payout', [AdminFinanceController::class, 'approvePayout'])->name('admin.finance.approve-payout');
    Route::post('finance/reject-payout', [AdminFinanceController::class, 'rejectPayout'])->name('admin.finance.reject-payout');
    Route::post('finance/bulk-approve-payouts', [AdminFinanceController::class, 'bulkApprovePayouts'])->name('admin.finance.bulk-approve-payouts');
    Route::post('finance/schedule-payouts', [AdminFinanceController::class, 'schedulePayouts'])->name('admin.finance.schedule-payouts');
    
    // ==========================================
    // SETTLEMENT HOLD ROUTES
    // ==========================================
    Route::post('finance/apply-manual-hold', [AdminFinanceController::class, 'applyManualHold'])->name('admin.finance.apply-manual-hold');
    Route::post('finance/release-hold', [AdminFinanceController::class, 'releaseHold'])->name('admin.finance.release-hold');
    Route::post('finance/update-settlement-rules', [AdminFinanceController::class, 'updateSettlementRules'])->name('admin.finance.update-settlement-rules');
    
    // ==========================================
    // LEAD MONETIZATION ROUTES
    // ==========================================
    Route::post('finance/update-lead-rules', [AdminFinanceController::class, 'updateLeadRules'])->name('admin.finance.update-lead-rules');
    Route::post('finance/update-ppc-rules', [AdminFinanceController::class, 'updatePpcRules'])->name('admin.finance.update-ppc-rules');
    Route::post('finance/update-ppl-rules', [AdminFinanceController::class, 'updatePplRules'])->name('admin.finance.update-ppl-rules');
    
    // ==========================================
    // PRICING ROUTES
    // ==========================================
    Route::post('finance/update-pricing', [AdminFinanceController::class, 'updatePricing'])->name('admin.finance.update-pricing');
    
    // ==========================================
    // TAX & INVOICE ROUTES
    // ==========================================
    Route::post('finance/update-tax-rules', [AdminFinanceController::class, 'updateTaxRules'])->name('admin.finance.update-tax-rules');
    Route::post('finance/generate-invoice', [AdminFinanceController::class, 'generateInvoice'])->name('admin.finance.generate-invoice');
    Route::get('finance/download-invoice/{id}', [AdminFinanceController::class, 'downloadInvoice'])->name('admin.finance.download-invoice');
    
    // ==========================================
    // REFUND ROUTES
    // ==========================================
    Route::post('finance/process-refund', [AdminFinanceController::class, 'processRefund'])->name('admin.finance.process-refund');
    Route::post('finance/update-refund-rules', [AdminFinanceController::class, 'updateRefundRules'])->name('admin.finance.update-refund-rules');
    Route::post('finance/approve-refund', [AdminFinanceController::class, 'approveRefund'])->name('admin.finance.approve-refund');
    Route::post('finance/reject-refund', [AdminFinanceController::class, 'rejectRefund'])->name('admin.finance.reject-refund');
    Route::post('finance/update-lead-refund-rules', [AdminFinanceController::class, 'updateLeadRefundRules'])->name('admin.finance.update-lead-refund-rules');
    
    // ==========================================
    // DISPUTE ROUTES
    // ==========================================
    Route::post('finance/resolve-dispute', [AdminFinanceController::class, 'resolveDispute'])->name('admin.finance.resolve-dispute');
    Route::post('finance/escalate-dispute', [AdminFinanceController::class, 'escalateDispute'])->name('admin.finance.escalate-dispute');
    Route::post('finance/upload-dispute-evidence', [AdminFinanceController::class, 'uploadDisputeEvidence'])->name('admin.finance.upload-dispute-evidence');
    Route::post('finance/update-dispute-rules', [AdminFinanceController::class, 'updateDisputeRules'])->name('admin.finance.update-dispute-rules');
    
    // ==========================================
    // BOOST CAMPAIGN ROUTES
    // ==========================================
    Route::post('finance/create-boost-campaign', [AdminFinanceController::class, 'createBoostCampaign'])->name('admin.finance.create-boost-campaign');
    Route::get('finance/boost-campaigns', [AdminFinanceController::class, 'getBoostCampaigns'])->name('admin.finance.boost-campaigns');
    Route::post('finance/update-boost-campaign', [AdminFinanceController::class, 'updateBoostCampaign'])->name('admin.finance.update-boost-campaign');
    
    // ==========================================
    // REVENUE ROUTES
    // ==========================================
    Route::get('finance/revenue-stats', [AdminFinanceController::class, 'getRevenueStats'])->name('admin.finance.revenue-stats');
    Route::get('finance/revenue-trends', [AdminFinanceController::class, 'getRevenueTrends'])->name('admin.finance.revenue-trends');
    
    // ==========================================
    // LEDGER & EXPORT ROUTES
    // ==========================================
    Route::get('finance/export-transactions', [AdminFinanceController::class, 'exportTransactions'])->name('admin.finance.export-transactions');
    Route::get('finance/export-ledger', [AdminFinanceController::class, 'exportLedger'])->name('admin.finance.export-ledger');
    Route::get('finance/export-tax-report', [AdminFinanceController::class, 'exportTaxReport'])->name('admin.finance.export-tax-report');
    
    // ==========================================
    // DASHBOARD STATS ROUTES
    // ==========================================
    Route::get('finance/dashboard-stats', [AdminFinanceController::class, 'getDashboardStats'])->name('admin.finance.dashboard-stats');
    Route::get('finance/commission-trends', [AdminFinanceController::class, 'getCommissionTrends'])->name('admin.finance.commission-trends');
    
    // ==========================================
    // SELLER EARNINGS ROUTES
    // ==========================================
    Route::get('finance/seller-earnings', [AdminFinanceController::class, 'getSellerEarnings'])->name('admin.finance.seller-earnings');
    Route::get('finance/seller-commission/{id}', [AdminFinanceController::class, 'getSellerCommission'])->name('admin.finance.seller-commission');
    
    // ==========================================
    // TRANSACTION LEDGER ROUTES
    // ==========================================
    Route::post('finance/filter-ledger', [AdminFinanceController::class, 'filterLedger'])->name('admin.finance.filter-ledger');
    Route::get('finance/transaction-details/{id}', [AdminFinanceController::class, 'getTransactionDetails'])->name('admin.finance.transaction-details');
    
    // ==========================================
    // BULK OPERATIONS ROUTES
    // ==========================================
    Route::post('finance/bulk-process-refunds', [AdminFinanceController::class, 'bulkProcessRefunds'])->name('admin.finance.bulk-process-refunds');
    Route::post('finance/bulk-release-holds', [AdminFinanceController::class, 'bulkReleaseHolds'])->name('admin.finance.bulk-release-holds');
    
    // ==========================================
    // NOTIFICATION ROUTES
    // ==========================================
    Route::post('finance/send-payout-notification', [AdminFinanceController::class, 'sendPayoutNotification'])->name('admin.finance.send-payout-notification');
    Route::post('finance/send-refund-notification', [AdminFinanceController::class, 'sendRefundNotification'])->name('admin.finance.send-refund-notification');
    
    
    
     Route::get('finance/get-all-sellers', [AdminFinanceController::class, 'getAllSellers']);
    Route::get('/finance/get-seller-caps', [AdminFinanceController::class, 'getSellerCaps']);
    Route::post('/finance/add-seller-cap', [AdminFinanceController::class, 'addSellerCap']);
    Route::post('/finance/remove-seller-cap', [AdminFinanceController::class, 'removeSellerCap']);
    Route::get('/finance/get-lead-config', [AdminFinanceController::class, 'getLeadConfig']);
    Route::post('/finance/update-lead-rules', [AdminFinanceController::class, 'updateLeadRules']);
    Route::get('/finance/get-revenue-data', [AdminFinanceController::class, 'getRevenueData']);
});
// ==================== SETTINGS ROUTES ====================
Route::prefix('settings')->name('admin.settings.')->group(function () {
    Route::get('tracking-tools', [TrackingToolsController::class, 'trackingtool'])->name('tracking-tools.index');
    Route::get('tracking-tools/create', [TrackingToolsController::class, 'create'])->name('tracking-tools.create');
    Route::post('tracking-tools', [TrackingToolsController::class, 'store'])->name('tracking-tools.store');
    Route::get('tracking-tools/{id}/edit', [TrackingToolsController::class, 'edit'])->name('tracking-tools.edit');
    Route::put('tracking-tools/{id}', [TrackingToolsController::class, 'update'])->name('tracking-tools.update');
    Route::delete('tracking-tools/{id}', [TrackingToolsController::class, 'destroy'])->name('tracking-tools.destroy');

    Route::get('general', [GeneralSettingsController::class, 'index'])->name('general');
    Route::post('settings', [GeneralSettingsController::class, 'save'])->name('save');

    Route::get('notifications', [GeneralSettingsController::class, 'notifications'])->name('notifications');
    Route::post('settings/notifications', [GeneralSettingsController::class, 'store'])->name('notifications.store');
    Route::get('settings/notifications/{id}/edit', [GeneralSettingsController::class, 'edit'])
        ->name('notifications.edit');

    Route::get('integrations', [GeneralSettingsController::class, 'integrations'])->name('integrations');
    Route::post('integrations', [GeneralSettingsController::class, 'integrationsUpdate']);

    Route::get('coupons', [GeneralSettingsController::class, 'coupons'])->name('coupons');
    Route::get('roles', [GeneralSettingsController::class, 'roles'])->name('roles');

    Route::get('qr-codes', [GeneralSettingsController::class, 'qrIndex'])->name('qr.index');
    Route::post('qr-codes/generate', [GeneralSettingsController::class, 'qrGenerate'])->name('qr.generate');

    Route::get('sitemap', [\App\Http\Controllers\admin\SitemapSettingsController::class, 'index'])->name('sitemap');
    Route::post('sitemap/generate', [\App\Http\Controllers\admin\SitemapSettingsController::class, 'generate'])->name('sitemap.generate');
    
    
    
   
});

// ==================== FRONTEND ROUTES ====================
Route::get('/get-category-products', [FrontendController::class, 'getCategoryProducts'])->name('category.products.ajax');

// ==================== ADMIN REPORT ROUTES ====================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/allreports', [ReportController::class, 'report'])->name('allreports');
    Route::post('/allreports/search', [ReportController::class, 'searchOrders'])
        ->name('reports.searchOrders');
    Route::get(
'seller-products',
[ReportController::class,'sellerProductReport']
)->name('reports.seller.products');

Route::get(
'/top-selling-products',
[ReportController::class,'sellerTopSellingReport']
)->name('reports.top.selling');

Route::get('reports/highest-rating', [ReportController::class, 'highestRating'])
    ->name('reports.highest.rating');

Route::get('reports/lowest-rating', [ReportController::class, 'lowestRating'])
    ->name('reports.lowest.rating');
    
Route::post('/reports/send-warning', [ReportController::class,'sendWarning'])
    ->name('reports.send-warning');
    

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/search-sales', [ReportController::class, 'searchSales'])->name('reports.searchSales');
    Route::post('/reports/search-orders', [ReportController::class, 'searchOrders'])->name('reports.searchOrders');
    Route::post('/reports/search-customers', [ReportController::class, 'searchCustomers'])->name('reports.searchCustomers');

    Route::get('reports/customers', [ReportController::class, 'customerReport'])
        ->name('reports.customers');
    Route::get('customers/active', [ReportController::class, 'activeCustomers'])
        ->name('customers.active');
    Route::post('/admin/customers/search/active', [ReportController::class, 'searchActive'])
        ->name('customers.search.active');

    Route::get('customer-report/export/csv', [ReportController::class, 'exportCustomerCSV'])
        ->name('customer.report.export.csv');
    Route::get('customer-report/export/excel', [ReportController::class, 'exportCustomerExcel'])
        ->name('customer.report.export.excel');
    Route::get('customer-report/export/pdf', [ReportController::class, 'exportCustomerPDF'])
        ->name('customer.report.export.pdf');
    
        // VERIFY (IMPORTANT)
    Route::post('/seller/verify', [ReportController::class, 'verify'])
        ->name('seller.verify');
    //negative review & comments Management
    Route::get(
'seller-negative-reviews',
[ReportController::class,'sellerNegativeReviews']
)->name('reports.seller-negative');

Route::post(
'ban-seller',
[ReportController::class,'banSeller']
)->name('reports.ban-seller');

Route::post(
'reject-product',
[ReportController::class,'rejectProduct']
)->name('reports.reject-product');


//comission section
Route::get(
'commission-report',
[ReportController::class,'commissionReport']
)->name('commission.report');


Route::get(
'commission-settings',
[ReportController::class,'commissionSettings']
)->name('commission.settings');

Route::post('/inquiry-settings', [ReportController::class, 'saveInquirySettings'])
->name('inquiry.settings.save');


Route::post(
'commission-settings-save',
[ReportController::class,'saveCommission']
)->name('commission.save');


Route::post('commission/save',[ReportController::class,'saveCommission'])->name('commission.save');

Route::get('/commission/delete/{id}',[ReportController::class,'deleteCommission'])->name('commission.delete');

    //Enquiries & Lead Management
    Route::get('enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
    Route::get('enquiries/assign', [EnquiryController::class, 'assign'])->name('enquiries.assign');
    Route::get('enquiries/status', [EnquiryController::class, 'changeStatus'])->name('enquiries.status');
    Route::get('enquiries/export', [EnquiryController::class, 'export'])->name('enquiries.export');
    


    //notification
    Route::get('/admin/get-notifications', [NotificationController::class, 'fetch'])
        ->name('notifications.fetch');

    //homepage
    Route::resource('/createabout', AboutSectionController::class)->names([
        'index' => 'about.index',
        'create' => 'about.create',
        'store' => 'about.store',
        'edit' => 'about.edit',
        'update' => 'about.update',
        'destroy' => 'about.destroy'
    ]);

    //homepage slider
    Route::resource('sliders', SliderController::class);
    Route::resource('newshighlights', NewsHighlightController::class);

    //homepage partners
    Route::get('partners', [PartnerController::class, 'index'])->name('partners.index');
    Route::post('partners/store', [PartnerController::class, 'storePartner'])->name('partners.store');
    Route::get('partners/{id}/edit', [PartnerController::class, 'editPartner'])->name('partners.edit');
    Route::put('partners/{id}', [PartnerController::class, 'updatePartner'])->name('partners.update');
    Route::delete('partners/{id}', [PartnerController::class, 'destroyPartner'])->name('partners.destroy');

    // Partners Content CRUD
    Route::post('partners-content/store', [PartnerController::class, 'storeContent'])->name('partners-content.store');
    Route::get('partners-content/{id}/edit', [PartnerController::class, 'editContent'])->name('partners-content.edit');
    Route::put('partners-content/{id}', [PartnerController::class, 'updateContent'])->name('partners-content.update');
    Route::delete('partners-content/{id}', [PartnerController::class, 'destroyContent'])->name('partners-content.destroy');

    //homepage footer section
    Route::resource('footers', \App\Http\Controllers\admin\FooterController::class);

   
});




// Add these routes after your existing report routes

// Sales Analytics
Route::get('/analytics/sales', [ReportController::class, 'salesAnalytics'])->name('admin.analytics.sales');

// Funnel Analytics  
Route::get('/analytics/funnel', [ReportController::class, 'funnelAnalytics'])->name('admin.analytics.funnel');

// Customer Analytics
Route::get('/analytics/customers', [ReportController::class, 'customerAnalytics'])->name('admin.analytics.customers');

// Seller Analytics
Route::get('/analytics/sellers', [ReportController::class, 'sellerAnalytics'])->name('admin.analytics.sellers');

// Promotion Analytics
Route::get('/analytics/promotion', [ReportController::class, 'promotionAnalytics'])->name('admin.analytics.promotion');

// Campaign ROI
Route::get('/analytics/campaign-roi', [ReportController::class, 'campaignROI'])->name('admin.analytics.campaign');

// Lead Analytics
Route::get('/analytics/leads', [ReportController::class, 'leadAnalytics'])->name('admin.analytics.leads');

// Analytics Graphs
Route::get('/analytics/graphs', [ReportController::class, 'analyticsGraphs'])->name('admin.analytics.graphs');

// AI Promotion Engine
Route::get('/ai/promotion-engine', [ReportController::class, 'aiPromotionEngine'])->name('admin.ai.promotion.engine');
Route::post('/ai/get-recommendations', [ReportController::class, 'getAIRecommendations'])->name('admin.ai.recommendations');

// AI Insights
Route::get('/ai/insights', [ReportController::class, 'aiInsights'])->name('admin.ai.insights');

// Dropshipping Analytics
Route::get('/analytics/dropshipping', [ReportController::class, 'dropshippingAnalytics'])->name('admin.analytics.dropshipping');

// Add this route with your other admin routes
Route::post('/products/boost', [ReportController::class, 'boostProduct'])->name('admin.products.boost');

// ==================== ADMIN ORDER ROUTES ====================
Route::prefix('admin/orders')->name('admin.orders.')->group(function () {
    // Order Monitoring routes
    Route::get('/lifecycle', [OrderController::class, 'lifecycle'])->name('lifecycle');
    Route::get('/cancellation', [OrderController::class, 'cancellation'])->name('cancellation');
    Route::get('/payment', [OrderController::class, 'payment'])->name('payment');
    Route::get('/payment/export', [OrderController::class, 'exportPayment'])->name('payment.export');
    
     // Payment details and actions
    Route::get('/payments/{id}', [OrderController::class, 'paymentDetails'])->name('payments.details');
    Route::get('/invoice/{id}', [OrderController::class, 'viewInvoice'])->name('invoice.view');
    Route::get('/invoice/validate/{id}', [OrderController::class, 'validateInvoice'])->name('invoice.validate');
    
    // Fraud management
    Route::post('/payments/{id}/suspicious', [OrderController::class, 'markSuspicious'])->name('payments.suspicious');
    Route::post('/payments/{id}/fraud', [OrderController::class, 'flagFraud'])->name('payments.fraud');
    Route::post('/payments/{id}/clear-fraud', [OrderController::class, 'clearFraudFlag'])->name('payments.clear-fraud');
    Route::post('/payments/{id}/run-fraud-detection', [OrderController::class, 'runFraudDetection'])->name('payments.run-fraud-detection');
    
    // Refund
    Route::post('/payments/{id}/refund', [OrderController::class, 'refundPayment'])->name('payments.refund');
    
        // Restored original order routes
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/show/{id}', [OrderController::class, 'show'])->name('show');
    Route::get('/processing', [OrderController::class, 'processing'])->name('processing');
    Route::get('/completed', [OrderController::class, 'completed'])->name('completed');
    Route::get('/out-for-delivery', [OrderController::class, 'outForDelivery'])->name('outfordelivery');
    Route::get('/refund', [OrderController::class, 'refund'])->name('refund');
    Route::get('/declined', [OrderController::class, 'declined'])->name('declined');
    Route::get('/failed', [OrderController::class, 'failed'])->name('failed');
    Route::get('/cancelled', [OrderController::class, 'cancelled'])->name('cancelled');
    // Existing routes remain unchanged
    Route::get('/processing', [OrderController::class, 'processing'])->name('processing');
    Route::get('/cancel-request', [OrderController::class, 'cancelRequest'])->name('cancel.request');
});



// In routes/web.php

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // ... your existing routes ...
    
    // Reports & Analytics Routes
    Route::get('reports-analytics', [App\Http\Controllers\admin\AdminReportAnalyticController::class, 'index'])
        ->name('reports.analytics');
        
    // Optional: AJAX endpoint for real-time data updates
    Route::get('reports-analytics/data', [App\Http\Controllers\admin\AdminReportAnalyticController::class, 'getAnalyticsData'])
        ->name('reports.analytics.data');
});









// SELLER MANAGEMENT
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/sellers', [App\Http\Controllers\admin\SellermanagementController::class, 'index'])
        ->name('sellers.index');

    Route::get('/sellers/{id}', [App\Http\Controllers\admin\SellermanagementController::class, 'show'])
        ->name('sellers.show');
        
        // Seller Management Routes
Route::post('/sellers/activate', [SellermanagementController::class, 'activate'])->name('sellers.activate');
Route::post('/sellers/deactivate', [SellermanagementController::class, 'deactivate'])->name('sellers.deactivate');
Route::get('/sellers/edit/{id}', [SellermanagementController::class, 'edit'])->name('sellers.edit');

    Route::post('/sellers/{id}/approve', [App\Http\Controllers\admin\SellermanagementController::class, 'approve'])
        ->name('sellers.approve');
        
    Route::post('/sellers/{id}/approve-platforms', [App\Http\Controllers\admin\SellermanagementController::class, 'approvePlatforms'])
        ->name('sellers.approve-platforms');

    Route::post('/sellers/{id}/reject', [App\Http\Controllers\admin\SellermanagementController::class, 'reject'])
        ->name('sellers.reject');

    Route::post('/sellers/{id}/digilocker', [App\Http\Controllers\admin\SellermanagementController::class, 'verifyDigilocker'])
        ->name('sellers.digilocker');
        
            // 🔥 NEW
    Route::post('sellers/{id}/verification', [App\Http\Controllers\admin\SellermanagementController::class, 'updateVerification'])->name('sellers.verify');
    
     Route::get('/blacklist', [SellermanagementController::class, 'blacklistIndex'])->name('blacklist.index');
    Route::post('/block-seller/{id}', [SellermanagementController::class, 'blockSeller'])->name('block.seller');
    Route::post('/block-product/{id}', [SellermanagementController::class, 'blockProduct'])->name('block.product');
    Route::post('/block-customer/{id}', [SellermanagementController::class, 'blockCustomer'])->name('block.customer');
    Route::post('/unblock/{id}', [SellermanagementController::class, 'unblock'])->name('unblock');
    
    // Audit Logs Routes
    Route::get('/audit-logs', [SellermanagementController::class, 'auditLogsIndex'])->name('audit-logs.index');
    Route::get('/seller-timeline/{id}', [SellermanagementController::class, 'sellerTimeline'])->name('seller.timeline');
    Route::post('/trigger-reverification/{id}', [SellermanagementController::class, 'triggerReVerification'])->name('trigger.reverification');


});


//multilevel commision
Route::post('admin/update-commission', [AdminFinanceController::class, 'updateCommission'])->name('admin.update.commission');
Route::post('admin/update-overrides', [AdminFinanceController::class, 'updateOverrides'])->name('admin.update.overrides');
Route::post('admin/update-settlement', [AdminFinanceController::class, 'updateSettlementRules'])->name('admin.update.settlement');
Route::post('admin/apply-hold', [AdminFinanceController::class, 'applyManualHold'])->name('admin.apply.hold');

Route::post('admin/get-commission-rates', [AdminFinanceController::class, 'getCommissionRates'])->name('admin.get.commission.rates');
Route::post('admin/save-commissions', [AdminFinanceController::class, 'saveCommissions'])->name('admin.save.commissions');
Route::post('admin/add-seller', [AdminFinanceController::class, 'addNewSeller'])->name('admin.add.seller');


// Add to your admin approval routes group
Route::prefix('admin/approval')->name('admin.approval.')->group(function () {
    // Product verification routes
    Route::post('/product/{id}/save-notes', [ApprovalController::class, 'saveAdminNotes']);
    Route::post('/product/{id}/auto-save-checklist', [ApprovalController::class, 'autoSaveChecklist']);
    Route::get('/product/{id}/validate-auto-order', [ApprovalController::class, 'validateAutoOrder']);
    Route::get('/product/{id}/track-dispatch', [ApprovalController::class, 'trackDispatch']);
    Route::get('/product/{id}/monitor-sla', [ApprovalController::class, 'monitorSLA']);
    Route::post('/product/{id}/remove-misleading', [ApprovalController::class, 'removeMisleadingContent']);
    Route::post('/product/{id}/flag-duplicate', [ApprovalController::class, 'flagDuplicate']);
    Route::post('/product/{id}/block-inappropriate', [ApprovalController::class, 'blockInappropriate']);
    Route::post('/product/{id}/approve-seller-removal', [ApprovalController::class, 'approveSellerRemoval']);
    Route::post('/product/{id}/reject-seller-removal', [ApprovalController::class, 'rejectSellerRemoval']);
    Route::get('/product/{id}/version/{versionId}', [ApprovalController::class, 'getVersion']);
    Route::post('/product/{id}/rollback/{versionId}', [ApprovalController::class, 'rollbackToVersion']);
    Route::get('/product/{id}/activity-logs', [ApprovalController::class, 'getActivityLogs']);
    
    
});


//multilevel commision


Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('marketplace-control', [AdminMarketplaceController::class, 'index'])->name('admin.marketplace-control');
    Route::get('marketplace/verify/{id}', [AdminMarketplaceController::class, 'verify'])->name('admin.marketplace.verify');
    Route::post('marketplace/approve/{entity}/{id}', [AdminMarketplaceController::class, 'approveEntity'])->name('admin.marketplace.approve');
    Route::post('marketplace/reject/{entity}/{id}', [AdminMarketplaceController::class, 'rejectEntity'])->name('admin.marketplace.reject');
    Route::get('marketplace/search-discovery', [AdminMarketplaceController::class, 'searchDiscovery'])->name('admin.marketplace.search-discovery');
    Route::get('marketplace/promotions', [AdminMarketplaceController::class, 'promotionsView'])->name('admin.marketplace.promotions');
    Route::get('marketplace/smart-suggestions', [AdminMarketplaceController::class, 'smartSuggestionsView'])->name('admin.marketplace.smart-suggestions');
   Route::post('marketplace/upload-multiple-banners', [AdminMarketplaceController::class, 'uploadMultipleBanners']);
    // Category Routes
    Route::post('marketplace/create-category', [AdminMarketplaceController::class, 'createCategory']);
    Route::put('marketplace/update-category/{id}', [AdminMarketplaceController::class, 'updateCategory']);
    Route::delete('marketplace/delete-category/{id}', [AdminMarketplaceController::class, 'deleteCategory']);
    Route::post('marketplace/toggle-category/{id}', [AdminMarketplaceController::class, 'toggleCategoryStatus']);
    Route::post('marketplace/add-subcategory', [AdminMarketplaceController::class, 'addSubcategory']);
    Route::delete('marketplace/delete-subcategory/{id}', [AdminMarketplaceController::class, 'deleteSubcategory']);
    
    // Banner Routes
    Route::post('marketplace/upload-multiple-banner', [AdminMarketplaceController::class, 'uploadMultipleBanner']);
    Route::post('marketplace/upload-single-banner', [AdminMarketplaceController::class, 'uploadSingleBanner']);
    Route::post('marketplace/upload-static-banner', [AdminMarketplaceController::class, 'uploadStaticBanner']);
    Route::post('marketplace/upload-dynamic-banner', [AdminMarketplaceController::class, 'uploadDynamicBanner']);
    Route::post('marketplace/approve-banner/{id}', [AdminMarketplaceController::class, 'approveBanner']);
    Route::delete('marketplace/delete-banner/{id}', [AdminMarketplaceController::class, 'deleteBanner']);
    
    // Boost Routes
    Route::post('marketplace/toggle-featured/{id}', [AdminMarketplaceController::class, 'toggleFeatured']);
    Route::post('marketplace/toggle-sponsored/{id}', [AdminMarketplaceController::class, 'toggleSponsored']);
    Route::post('marketplace/add-featured', [AdminMarketplaceController::class, 'addFeaturedProduct']);
    
    // Template Routes
    Route::post('marketplace/upload-template', [AdminMarketplaceController::class, 'uploadTemplate']);
    Route::post('marketplace/assign-template', [AdminMarketplaceController::class, 'assignTemplate']);
    Route::post('marketplace/assign-to-seller', [AdminMarketplaceController::class, 'assignTemplateToSeller']);
    
    // SEO Routes
    Route::post('marketplace/block-keyword', [AdminMarketplaceController::class, 'blockSpamKeyword']);
    Route::post('marketplace/suggest-seo/{productId}', [AdminMarketplaceController::class, 'suggestSEO']);
    Route::get('marketplace/seo-audit', [AdminMarketplaceController::class, 'runSEOAudit']);
    
    // Operation Area Routes
    Route::post('marketplace/validate-area/{id}', [AdminMarketplaceController::class, 'validateArea']);
    Route::post('marketplace/invalidate-area/{id}', [AdminMarketplaceController::class, 'invalidateArea']);
    
    // Search Routes
    Route::post('marketplace/update-ranking-rules', [AdminMarketplaceController::class, 'updateRankingRules']);
    Route::post('marketplace/pin-product/{id}', [AdminMarketplaceController::class, 'pinProduct']);
    Route::post('marketplace/unpin-product/{id}', [AdminMarketplaceController::class, 'unpinProduct']);
    Route::get('marketplace/search-products', [AdminMarketplaceController::class, 'searchProducts']);
    
    // Dynamic Page Routes
    Route::post('marketplace/create-page', [AdminMarketplaceController::class, 'createDynamicPage']);
    Route::post('marketplace/approve-page/{id}', [AdminMarketplaceController::class, 'approveDynamicPage']);
    Route::post('marketplace/publish-page/{id}', [AdminMarketplaceController::class, 'publishDynamicPage']);
    Route::delete('marketplace/delete-page/{id}', [AdminMarketplaceController::class, 'deleteDynamicPage']);
    
    // Smart Suggestions
    Route::get('marketplace/smart-suggestions-data', [AdminMarketplaceController::class, 'getSmartSuggestions'])->name('admin.marketplace.get-smart-suggestions');
    
    // Promotion Routes
    Route::post('marketplace/create-campaign', [AdminMarketplaceController::class, 'createCampaign']);
    Route::post('marketplace/end-campaign/{id}', [AdminMarketplaceController::class, 'endCampaign']);
    
    // Advertisement Routes
    Route::post('marketplace/create-ad', [AdminMarketplaceController::class, 'createAdvertisement']);
    
    // Export
    Route::get('marketplace/export-data', [AdminMarketplaceController::class, 'exportMarketplaceData']);
});






// Add this route INSIDE your admin group
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    
    Route::prefix('marketplace')->group(function () {
        
        // Your existing routes (keep them as is)
     
        
        // ADD THIS ROUTE - Important!
        Route::post('/add-multiple-boosts', [AdminMarketplaceController::class, 'addMultipleBoosts']);
        
        // Other routes
        Route::post('/add-boost', [AdminMarketplaceController::class, 'addBoost']);
        Route::post('/remove-boost/{id}', [AdminMarketplaceController::class, 'removeBoost']);
        Route::get('/get-boost/{id}', [AdminMarketplaceController::class, 'getBoost']);
        Route::post('/update-boost/{id}', [AdminMarketplaceController::class, 'updateBoost']);
        Route::post('/toggle-product-status/{id}', [AdminMarketplaceController::class, 'toggleProductStatus']);
        Route::get('/export-boosted-products', [AdminMarketplaceController::class, 'exportBoostedProducts']);
        
        // ========== RECOMMENDATION ENGINE ROUTES ==========
        Route::prefix('recommendations')->group(function () {
            Route::get('/', [AdminMarketplaceController::class, 'recommendationsIndex'])->name('admin.marketplace.recommendations.index');
            Route::post('/store', [AdminMarketplaceController::class, 'recommendationStore'])->name('admin.marketplace.recommendations.store');
            Route::get('/search-products', [AdminMarketplaceController::class, 'searchProducts'])->name('admin.marketplace.recommendations.searchProducts');
            Route::get('/{id}/get', [AdminMarketplaceController::class, 'getRecommendations'])->name('admin.marketplace.recommendations.get');
            Route::post('/{id}/save', [AdminMarketplaceController::class, 'saveRecommendations'])->name('admin.marketplace.recommendations.save');
            Route::get('/{id}/edit', [AdminMarketplaceController::class, 'recommendationEdit'])->name('admin.marketplace.recommendations.edit');
            Route::put('/{id}', [AdminMarketplaceController::class, 'recommendationUpdate'])->name('admin.marketplace.recommendations.update');
            Route::delete('/{id}', [AdminMarketplaceController::class, 'recommendationDestroy'])->name('admin.marketplace.recommendations.destroy');
            Route::post('/toggle-status/{id}', [AdminMarketplaceController::class, 'recommendationToggleStatus'])->name('admin.marketplace.recommendations.toggle-status');
        });
    });
});



// routes/web.php

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        
        // AJAX Routes
        Route::get('/transactions', [WalletController::class, 'getTransactions'])->name('transactions');
        Route::get('/users', [WalletController::class, 'getUsers'])->name('users');
        Route::get('/withdrawals', [WalletController::class, 'getWithdrawals'])->name('withdrawals');
        Route::get('/dashboard-summary', [WalletController::class, 'getDashboardSummary'])->name('dashboard-summary');
        
        // POST Routes
        Route::post('/freeze', [WalletController::class, 'freezeWallet'])->name('freeze');
        Route::post('/unfreeze', [WalletController::class, 'unfreezeWallet'])->name('unfreeze');
        Route::post('/credit', [WalletController::class, 'manualCredit'])->name('credit');
        Route::post('/debit', [WalletController::class, 'manualDebit'])->name('debit');
        
        // Withdrawal actions
        Route::post('/withdrawal/{id}/approve', [WalletController::class, 'approveWithdrawal'])->name('withdrawal.approve');
        Route::post('/withdrawal/{id}/reject', [WalletController::class, 'rejectWithdrawal'])->name('withdrawal.reject');
        Route::post('/withdrawal/{id}/hold', [WalletController::class, 'holdWithdrawal'])->name('withdrawal.hold');
        
           Route::get('/history', [WalletController::class, 'getHistoryLogs'])->name('history');
        // Add this inside your admin wallet routes group
        Route::get('/refunds', [WalletController::class, 'getRefunds'])->name('refunds');
        Route::post('/refund/{id}/approve', [WalletController::class, 'approveRefund'])->name('refund.approve');
        // Add this route for fetching team members
Route::get('/teams/get-members', [WalletController::class, 'getTeamMembers'])->name('teams.get-members');
Route::get('/aget-staff-members', [WalletController::class, 'getStaffMembers'])->name('get-staff-members');

Route::get('/get-order-details', [WalletController::class, 'getOrderDetails'])->name('get-order-details');

Route::post('/disburse-salary', [WalletController::class, 'disburseSalary'])->name('disburse-salary');


Route::get('/get-bank-accounts', [WalletController::class, 'getBankAccounts'])->name('get-bank-accounts');
Route::post('/add-bank-account', [WalletController::class, 'addBankAccount'])->name('add-bank-account');
Route::post('/self-withdraw', [WalletController::class, 'selfWithdraw'])->name('self-withdraw');
    });
});





Route::prefix('staff')->name('admin.staff.')->group(function () {

    Route::get('/directory', [StaffController::class, 'directory'])->name('directory');
    Route::get('/bank-payroll', [StaffController::class, 'bankPayroll'])->name('bank-payroll');
    Route::get('/salary-payments', [StaffController::class, 'salaryPayments'])->name('salary-payments');
    Route::get('/create', [StaffController::class, 'create'])->name('create');
    Route::post('/store', [StaffController::class, 'store'])->name('store');
    Route::get('/{id}', [StaffController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [StaffController::class, 'edit'])->name('edit');
    Route::put('/{id}', [StaffController::class, 'update'])->name('update');
    Route::delete('/{id}', [StaffController::class, 'destroy'])->name('destroy');
    
    // Status update route - FIXED PATH
    Route::post('/update-status', [StaffController::class, 'updateStatus'])->name('update-status');
    
    Route::get('/export', [StaffController::class, 'export'])->name('export');
    Route::get('/access-permissions', [StaffController::class, 'accessPermissions'])->name('access-permissions');

    // Staff specific pages
    Route::get('/profile/{id}', [StaffController::class, 'profile'])->name('profile');
    Route::get('/bank-payroll/{id}/view', [StaffController::class, 'bankPayrollView'])->name('bank-payroll.view');
    Route::get('/salary-payments/{id}/view', [StaffController::class, 'salaryPaymentsView'])->name('salary-payments.view');
    Route::get('/access-permissions/{id}/view', [StaffController::class, 'accessPermissionsView'])->name('access-permissions.view');
});




// ==================== TAX & INVOICE ROUTES ====================
// Add these inside your admin finance routes group
Route::prefix('admin/finance')->name('admin.finance.')->group(function () {
    
    // Tax & Invoice Routes (with proper names)
    Route::post('/add-tax-region', [AdminFinanceController::class, 'addTaxRegion'])->name('add-tax-region');
    Route::post('/add-currency', [AdminFinanceController::class, 'addCurrency'])->name('add-currency');
    Route::delete('/delete-tax-region/{index}', [AdminFinanceController::class, 'deleteTaxRegion'])->name('delete-tax-region');
    Route::delete('/delete-currency/{index}', [AdminFinanceController::class, 'deleteCurrency'])->name('delete-currency');
    Route::post('/generate-invoice', [AdminFinanceController::class, 'generateInvoice'])->name('generate-invoice');
    Route::post('/download-invoice', [AdminFinanceController::class, 'downloadInvoice'])->name('download-invoice');
    Route::post('/send-invoice', [AdminFinanceController::class, 'sendInvoiceEmail'])->name('send-invoice');
    Route::post('/export-report', [AdminFinanceController::class, 'exportReport'])->name('export-report');
    Route::post('/validate-gst', [AdminFinanceController::class, 'validateGST'])->name('validate-gst');
    Route::post('/save-all-tax-settings', [AdminFinanceController::class, 'saveAllTaxSettings'])->name('save-all-tax-settings');
    Route::post('/save-pricing-discount-rules', [AdminFinanceController::class, 'savePricingDiscountRules'])->name('save-pricing-discount-rules');
    Route::post('/release-hold', [AdminFinanceController::class, 'releaseHold'])->name('release-hold');
    
    // Auto Escalation Routes
    Route::post('/add-escalation-rule', [AdminFinanceController::class, 'addEscalationRule'])->name('add-escalation-rule');
    Route::delete('/remove-escalation-rule/{index}', [AdminFinanceController::class, 'removeEscalationRule'])->name('remove-escalation-rule');
    
    // Seller Daily Caps Routes (already exist but ensure they have names)
    Route::get('/get-all-sellers', [AdminFinanceController::class, 'getAllSellers'])->name('get-all-sellers');
    Route::get('/get-seller-caps', [AdminFinanceController::class, 'getSellerCaps'])->name('get-seller-caps');
    Route::post('/add-seller-cap', [AdminFinanceController::class, 'addSellerCap'])->name('add-seller-cap');
    Route::post('/remove-seller-cap', [AdminFinanceController::class, 'removeSellerCap'])->name('remove-seller-cap');
    Route::get('/get-lead-config', [AdminFinanceController::class, 'getLeadConfig'])->name('get-lead-config');
    Route::get('/get-lead-rules', [AdminFinanceController::class, 'getLeadRules'])->name('get-lead-rules');
    Route::post('/update-lead-rules', [AdminFinanceController::class, 'updateLeadRules'])->name('update-lead-rules');
    Route::get('/get-revenue-data', [AdminFinanceController::class, 'getRevenueData'])->name('get-revenue-data');
    
    // Fraud Rules Routes
    Route::get('/fraud-rules', [AdminFinanceController::class, 'getFraudRules'])->name('fraud-rules');
    Route::get('/fraud-rules-details', [AdminFinanceController::class, 'getFraudRulesDetails'])->name('fraud-rules-details');
    Route::post('/toggle-fraud-rule', [AdminFinanceController::class, 'toggleFraudRule'])->name('toggle-fraud-rule');
    Route::post('/initialize-fraud-rules', [AdminFinanceController::class, 'initializeFraudRules'])->name('initialize-fraud-rules');
    
    // Global Rules Routes
    Route::post('/update-global-rules', [AdminFinanceController::class, 'updateGlobalRules'])->name('update-global-rules');
    Route::get('/get-global-rules', [AdminFinanceController::class, 'getGlobalRules'])->name('get-global-rules');
    Route::get('/get-rule', [AdminFinanceController::class, 'getRule'])->name('get-rule');
    Route::post('/delete-rule', [AdminFinanceController::class, 'deleteRule'])->name('delete-rule');
});

// ==================== MASTER SYSTEM CONTROL ROUTES ====================
Route::prefix('admin/settings')->name('admin.settings.')->middleware('auth:web,admin_staff')->group(function () {
    Route::get('/master-control', [\App\Http\Controllers\admin\SettingsController::class, 'masterControl'])->name('master_control');
    Route::post('/save-master-config', [\App\Http\Controllers\admin\SettingsController::class, 'saveMasterConfig'])->name('save_master_config');
    
    
      Route::get('/system', [SettingsController::class, 'systemSettings'])->name('system');
    Route::post('/system/security', [SettingsController::class, 'saveSecuritySettings'])->name('system.security.save');
    Route::post('/system/data', [SettingsController::class, 'saveDataControlSettings'])->name('system.data.save');
    Route::post('/system/platform', [SettingsController::class, 'savePlatformSettings'])->name('system.platform.save');
    
    // Data Control Operations
    Route::get('/system/export', [SettingsController::class, 'exportData'])->name('system.export');
    Route::post('/system/backup/create', [SettingsController::class, 'createBackup'])->name('system.backup.create');
    Route::post('/system/backup/delete', [SettingsController::class, 'deleteBackup'])->name('system.backup.delete');
    Route::post('/system/restore', [SettingsController::class, 'restoreBackup'])->name('system.restore');
    Route::post('/system/purge', [SettingsController::class, 'purgeLogs'])->name('system.purge');
    Route::post('/system/deletion/approve/{id}', [SettingsController::class, 'approveDeletion'])->name('system.deletion.approve');
    Route::post('/system/deletion/reject/{id}', [SettingsController::class, 'rejectDeletion'])->name('system.deletion.reject');
    
    // Tax Rates AJAX
    Route::post('/tax-rate/save', [SettingsController::class, 'saveTaxRate'])->name('tax-rate.save');
    Route::post('/tax-rate/delete', [SettingsController::class, 'deleteTaxRate'])->name('tax-rate.delete');
    Route::post('/tax-rate/toggle', [SettingsController::class, 'toggleTaxRateStatus'])->name('tax-rate.toggle');
    Route::post('/tax-rate/set-default', [SettingsController::class, 'setDefaultTaxRate'])->name('tax-rate.set-default');

    // Boost Settings
    Route::post('/boost/store', [SettingsController::class, 'storeBoost'])->name('boost.store');
    Route::post('/boost/update/{id}', [SettingsController::class, 'updateBoost'])->name('boost.update');
    Route::delete('/boost/delete/{id}', [SettingsController::class, 'deleteBoost'])->name('boost.delete');
    Route::post('/boost/toggle/{id}', [SettingsController::class, 'toggleBoostStatus'])->name('boost.toggle');

    // ---- Data Control ----
    Route::get('/data-export', [SettingsController::class, 'dataExport'])->name('data.export');
    Route::post('/data-retention/save', [SettingsController::class, 'saveDataRetention'])->name('data.retention.save');
    Route::post('/data-retention/run', [SettingsController::class, 'runDataRetention'])->name('data.retention.run');
    Route::get('/system/deletion', [SettingsController::class, 'getDeletionRequests'])->name('system.deletion.list');
    Route::post('/user-deletion/process/{id}', [SettingsController::class, 'processUserDeletion'])->name('user.deletion.process');
    Route::post('/backup/create-now', [SettingsController::class, 'createBackupNow'])->name('backup.create');
    Route::delete('/backup/delete/{filename}', [SettingsController::class, 'deleteBackupFile'])->name('backup.delete');

    // ---- Fraud Protection ----
    Route::get('/fraud-dashboard', [SettingsController::class, 'fraudDashboard'])->name('fraud.dashboard');
    Route::post('/fraud/block-ip', [SettingsController::class, 'blockSuspiciousIp'])->name('fraud.block.ip');
    Route::post('/fraud/unblock-ip', [SettingsController::class, 'unblockIp'])->name('fraud.unblock.ip');
    Route::post('/fraud/save-settings', [SettingsController::class, 'saveFraudSettings'])->name('fraud.settings.save');
    Route::post('/fraud/dismiss-alert/{id}', [SettingsController::class, 'dismissFraudAlert'])->name('fraud.alert.dismiss');
    Route::post('/fraud/flag-duplicate/{id}', [SettingsController::class, 'flagDuplicateProduct'])->name('fraud.product.flag');
});


//grivance section
// ==================== GRIEVANCE & DISPUTE SYSTEM ROUTES ====================
Route::prefix('admin/grievance')->name('admin.grievance.')->middleware('auth:web,admin_staff')->group(function () {
    Route::get('/', [\App\Http\Controllers\admin\GrievanceController::class, 'index'])->name('index');
    Route::post('/store', [\App\Http\Controllers\admin\GrievanceController::class, 'store'])->name('store');
    Route::get('/get/{id}', [\App\Http\Controllers\admin\GrievanceController::class, 'show'])->name('show');
    Route::post('/{id}/assign', [\App\Http\Controllers\admin\GrievanceController::class, 'assign'])->name('assign');
    Route::post('/{id}/resolve', [\App\Http\Controllers\admin\GrievanceController::class, 'resolve'])->name('resolve');
    Route::post('/{id}/escalate', [\App\Http\Controllers\admin\GrievanceController::class, 'escalate'])->name('escalate');
    Route::post('/reply', [\App\Http\Controllers\admin\GrievanceController::class, 'reply'])->name('reply');
    
    // Escalation Matrix Routes
    Route::post('/escalation-rule/store', [\App\Http\Controllers\admin\GrievanceController::class, 'storeEscalationRule'])->name('storeEscalationRule');
    Route::delete('/escalation-rule/{id}', [\App\Http\Controllers\admin\GrievanceController::class, 'deleteEscalationRule'])->name('escalation-rule.delete');
    Route::post('/priority-settings/update', [\App\Http\Controllers\admin\GrievanceController::class, 'updatePrioritySettings'])->name('priority-settings.update');
});

// Alias route for admin.grievances.show (some views use plural 'grievances')
Route::get('admin/grievances/get/{id}', [\App\Http\Controllers\admin\GrievanceController::class, 'show'])
    ->name('admin.grievances.show')
    ->middleware('auth:web,admin_staff');

  Route::prefix('admin/enquiry')->name('admin.enquiry.')->middleware('auth:web,admin_staff')->group(function () {
        Route::get('/', [\App\Http\Controllers\admin\EnquiryController::class, 'index'])->name('index');
        Route::get('/get/{id}', [\App\Http\Controllers\admin\EnquiryController::class, 'show'])->name('show');
        Route::post('/{id}/assign', [\App\Http\Controllers\admin\EnquiryController::class, 'storeAssign'])->name('storeAssign');
        Route::post('/{id}/update-status', [\App\Http\Controllers\admin\EnquiryController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/export', [\App\Http\Controllers\admin\EnquiryController::class, 'export'])->name('export');
        Route::post('/escalate', [\App\Http\Controllers\admin\EnquiryController::class, 'escalate'])->name('escalate');
    });

// ==================== CHAT SYSTEM ROUTES ====================
Route::prefix('chat')->name('chat.')->middleware('auth:web,customer')->group(function () {
    Route::get('/communication-center', [ChatController::class, 'index'])->name('communication-center');
    Route::get('/conversations', [ChatController::class, 'conversations'])->name('conversations');
    Route::get('/conversations/{id}', [ChatController::class, 'getConversation'])->name('get-conversation');
    Route::post('/messages/send', [ChatController::class, 'sendMessage'])->name('send-message');
    Route::post('/conversations/create', [ChatController::class, 'createConversation'])->name('create-conversation');
    Route::get('/internal-support', [ChatController::class, 'internalSupport'])->name('internal-support');
    Route::post('/internal-support/create', [ChatController::class, 'createInternalSupport'])->name('create-internal-support');
    Route::post('/internal-support/{id}/update', [ChatController::class, 'updateInternalSupport'])->name('update-internal-support');
    Route::get('/internal-support/{id}', [ChatController::class, 'getInternalSupport'])->name('get-internal-support');
    Route::get('/broadcast-messaging', [ChatController::class, 'broadcastMessaging'])->name('broadcast-messaging');
    Route::post('/broadcast/send', [ChatController::class, 'sendBroadcast'])->name('send-broadcast');
    Route::get('/stats', [ChatController::class, 'getStats'])->name('stats');
    Route::post('/ashmita/message', [ChatController::class, 'ashmitaMessage'])->name('ashmita.message');
    Route::get('/ashmita/history', [ChatController::class, 'ashmitaHistory'])->name('ashmita.history');
    Route::delete('/ashmita/clear', [ChatController::class, 'ashmitaClearHistory'])->name('ashmita.clear');
    Route::get('/admin/messages/{sellerId}', [ChatController::class, 'getMessages'])->name('admin.messages');
    Route::post('/admin/send', [ChatController::class, 'sendMessage'])->name('admin.send');
    Route::get('/admin/customer-messages/{customerId}', [ChatController::class, 'getCustomerMessages'])->name('admin.customer-messages');
    Route::post('/admin/customer/send', [ChatController::class, 'sendCustomerMessage'])->name('admin.customer.send');
});

// ==================== LOCATION ROUTES ====================
Route::post('/update-current-location', [App\Http\Controllers\vtravel\HomeController::class, 'updateCurrentLocation'])->name('update.current.location');

// ==================== DYNAMIC ROUTING ====================
Route::group(['prefix' => '/', 'middleware' => 'auth:web,admin_staff'], function () {
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])
        ->name('third')
        ->where(['first' => '^[^.]+$', 'second' => '^[^.]+$', 'third' => '^[^.]+$']);

    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])
        ->name('second')
        ->where(['first' => '^[^.]+$', 'second' => '^[^.]+$']);

    Route::get('{any}', [RoutingController::class, 'root'])
        ->name('any')
        ->where('any', '^[^.]+$');
});

// ==================== FRONTEND AUTH ROUTES ====================
Route::get('/manager/orders/{order}/products', [AdminTicketController::class, 'getProducts']);
Route::get('/brand/orders/{order}/products', [AdminTicketController::class, 'getProducts']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('frontend.register');
Route::post('/register', [AuthController::class, 'register'])->name('frontend.register');
Route::post('/logout', [AuthController::class, 'logout'])->name('frontend.logout');
Route::post('/login', [AuthController::class, 'login'])->name('frontend.login.submit');
Route::post('admin/finance/update-leads-boost-settings', [AdminFinanceController::class, 'updateLeadsBoostSettings'])->name('admin.finance.update-leads-boost-settings');

// Admin Panel AI Assistant Chat
Route::post('/admin/ai-chat', [\App\Http\Controllers\Admin\AdminChatController::class, 'handleChat'])->name('admin.ai-chat')->middleware('auth:web,admin_staff');

Route::get('/proxy/yt-thumbnail', function (\Illuminate\Http\Request $request) {
    $url = $request->query('url');
    if (!$url) return response()->json(['error' => 'No URL'], 400);
    try {
        $content = file_get_contents($url);
        return response($content, 200)->header('Content-Type', 'image/jpeg')->header('Access-Control-Allow-Origin', '*');
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch'], 500);
    }
});

