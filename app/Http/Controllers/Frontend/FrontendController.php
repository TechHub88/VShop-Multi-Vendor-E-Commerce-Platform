<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Models\Slider;
use App\Models\Category;
use App\Models\About;
use App\Models\Partner;
use App\Models\PartnersContent;
use App\Models\NewsHighlight;
use App\Models\Footer;
use App\Models\Product;
class FrontendController extends Controller
{
  
    // Dashboard Page
  public function dashboard()
    {
        // Fetch all data from respective tables
           $sliders = Slider::all();     // Slider banners
       $categories = Category::with('products')->get();  // Home categories
        $about = About::first();                         // About us section
        $partners = Partner::all();               // Partner logos
        $partnerContent = PartnersContent::first();     
        $features = NewsHighlight::all(); 
 
      // Category wise products
    $categoryProducts = [];
    foreach ($categories as $category) {
        $categoryProducts[$category->id] =
            Product::where('category_id', $category->id)->get();
    }

        $footer= Footer::first();
        $products = Product::all();      // Products

        return view('frontend.dashboard', compact(
            'sliders', 'categories', 'about', 'partners', 'features', 'products','partnerContent','footer','categoryProducts'
        ));
    }
    
     public function Home()
    {
        // Fetch all data from respective tables
           $sliders = Slider::all();     // Slider banners
       $categories = Category::with('products')->get();  // Home categories
        $about = About::first();                         // About us section
        $partners = Partner::all();               // Partner logos
        $partnerContent = PartnersContent::first();     
        $features = NewsHighlight::all(); 
 
       // Category wise products
    $categoryProducts = [];
    foreach ($categories as $category) {
        $categoryProducts[$category->id] =
            Product::where('category_id', $category->id)->get();
    }

        $footer= Footer::first();
       $products = Product::select('id','name','sku','thumbnail','category_id')->get();


        return view('frontend.Home', compact(
            'sliders', 'categories', 'about', 'partners', 'features', 'products','partnerContent','footer','categoryProducts'
        ));
    }

    public function showChangePasswordForm()
    {
        return view('frontend.change-password'); // create this Blade view
    }

    /**
     * Update the customer's password
     */
    public function updatePassword(Request $request)
    {
        // Validate input
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user(); // type hint fixes Intelephense warning

        // Check if current password is correct
        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        $customer->password = Hash::make($request->new_password);
        $customer->save();

        return back()->with('success', 'Password updated successfully');
    }
    // Add this method to your FrontendController
public function getCategoryProducts(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id'
    ]);

    $products = Product::where('category_id', $request->category_id)
        ->select('id', 'name', 'sku', 'thumbnail', 'category_id')
        ->get();

    return response()->json([
        'success' => true,
        'products' => $products
    ]);
}
}
