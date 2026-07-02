<?php

namespace App\Http\Controllers\vtravel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class CustomerProfileController extends Controller
{

   public function index()
    {
        $customer = Auth::guard('customer')->user();

        return view('vtravel.profile', compact('customer'));
    }
     public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'required|string|max:15',
            'gstin'            => 'nullable|string|max:20',
            'country'          => 'nullable|string|max:100',
            'state'            => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
            'address_line1'    => 'nullable|string|max:255',
            'pincode'          => 'nullable|string|max:10',
            'profile_picture'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ✅ Update basic fields
        $customer->update($request->only([
            'name',
            'phone',
            'gstin',
            'country',
            'state',
            'city',
            'address_line1',
            'pincode',
        ]));

        // ✅ Handle profile picture upload
        if ($request->hasFile('profile_picture')) {

            // delete old image if exists
            if ($customer->profile_picture && file_exists(public_path($customer->profile_picture))) {
                unlink(public_path($customer->profile_picture));
            }

            $image = $request->file('profile_picture');
            $filename = 'profile_' . time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('vtravelassets/uploads/profile'), $filename);

            $customer->profile_picture = 'vtravelassets/uploads/profile/' . $filename;
            $customer->save();
        }

        return back()->with('success', 'Profile updated successfully.');
    }
}
