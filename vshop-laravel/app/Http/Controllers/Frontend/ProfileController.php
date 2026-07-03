<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\About;
use App\Models\Footer;
use App\Models\Order;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
      // ✔ Show Profile Page
     // ✔ Show profile page
   public function edit()
{
    $user        = Auth::guard('customer')->user();
    $about       = About::first();
    $categories  = Category::all();
    $footer      = Footer::first();

    // Fetch brands for the dropdown
    $brands = Brand::select('id', 'name')
                ->distinct()
                ->get();

    return view('frontend.profile', compact('user', 'about', 'categories', 'footer', 'brands'));
}


public function update(Request $request)
{
    $user = Auth::guard('customer')->user();
    $action = $request->input('action');

  if ($action === 'apply_partner') {

    $request->validate([
        'brand_id'  => 'required|exists:brands,id',
        'branch_id' => 'required|exists:branches,id',
    ]);

    // 👉 FETCH user_id FROM selected branch
    $branchUserId = DB::table('branches')
                        ->where('id', $request->branch_id)
                        ->value('user_id');

    // Save partner details in customers table
    $user->brand_id  = $request->brand_id;
    $user->branch_id = $request->branch_id;
    $user->user_id   = $branchUserId; // ⭐ SET USER ID FROM BRANCH TABLE
    $user->status    = 'pending';

    $user->save();

    return redirect()->back()->with('success', 'Partner application submitted successfully!');
}

    // ---- NORMAL PROFILE UPDATE ----
    $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email',
    ]);

    $user->name         = $request->name;
    $user->email        = $request->email;
    $user->phone        = $request->phone;
    $user->gstin        = $request->gstin;
    $user->country      = $request->country;
    $user->state        = $request->state;
    $user->city         = $request->city;
    $user->address_line1 = $request->address_line1;
    $user->pincode      = $request->pincode;

    if ($request->hasFile('profile_picture')) {
        $image    = $request->file('profile_picture');
        $filename = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('uploads/profile/'), $filename);
        $user->profile_picture = $filename;
    }

    $user->save();

    $user->checkAndRewardProfileCompletion();

    return redirect()->back()->with('success', 'Profile updated successfully!');
}



    
public function fetchBranches(Request $request)
{
    $brandId = $request->brand_id;

    if (!$brandId) {
        return response()->json([], 200);
    }

    $branches = \App\Models\Branch::where('brand_id', $brandId)
                   ->select('id', 'address', 'city', 'pincode')
                   ->get();

    return response()->json($branches, 200);
}


// In your controller (FrontendController.php or similar)
public function partnerPage()
{
      $about       = About::first();
    $categories  = Category::all();
    $footer      = Footer::first();
    // Simple static page - no database queries needed
    return view('frontend.partner',compact('about','categories','footer'));
}


}
