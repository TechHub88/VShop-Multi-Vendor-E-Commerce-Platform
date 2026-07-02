<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeTerm;
use Illuminate\Http\Request;

class SellerAttributeController extends Controller
{
    /* =======================
       INDEX – Seller Attributes
    ======================= */
    public function index()
    {
        $seller = auth()->guard('seller')->user();

        $attributes = Attribute::where('created_by', 'seller')
            ->where('created_by_id', $seller->id)
            ->latest()
            ->get();

        return view('vshop.seller.attributes.index', compact('attributes'));
    }


    /* =======================
       STORE ATTRIBUTE + TERMS
    ======================= */
    public function store(Request $request)
    {
        $seller = auth()->guard('seller')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'terms' => 'nullable|array',
            'terms.*' => 'required|string|max:255',
        ]);

        /* ===== CREATE ATTRIBUTE ===== */
        $attribute = Attribute::create([
            'name' => $request->name,

            // ⭐ Seller ownership
            'created_by' => 'seller',
            'created_by_id' => $seller->id,
            'status' => 'pending',
        ]);

        /* ===== CREATE TERMS ===== */
        if ($request->has('terms')) {
            foreach ($request->terms as $term) {
                AttributeTerm::create([
                    'attribute_id' => $attribute->id,
                    'term' => $term,

                    // ⭐ Seller ownership
                    'created_by' => 'seller',
                    'created_by_id' => $seller->id,
                    'status' => 'pending',
                ]);
            }
        }

        return back()->with('success', 'Attribute submitted for admin approval.');
    }


    /* =======================
       DELETE ATTRIBUTE
    ======================= */
    public function destroy($id)
    {
        $seller = auth()->guard('seller')->user();

        $attribute = Attribute::where('id', $id)
            ->where('created_by', 'seller')
            ->where('created_by_id', $seller->id)
            ->firstOrFail();

        $attribute->delete();

        return back()->with('success', 'Attribute deleted successfully.');
    }


    /* =======================
       TERMS PAGE
    ======================= */
    public function terms($id)
    {
        $seller = auth()->guard('seller')->user();

        $attribute = Attribute::where('id', $id)
            ->where('created_by', 'seller')
            ->where('created_by_id', $seller->id)
            ->with('terms')
            ->firstOrFail();

        return view('vshop.seller.attributes.terms', compact('attribute'));
    }


    /* =======================
       STORE TERM
    ======================= */
    public function termsStore(Request $request, $id)
    {
        $seller = auth()->guard('seller')->user();

        $request->validate([
            'term' => 'required|string|max:255',
        ]);

        $attribute = Attribute::where('id', $id)
            ->where('created_by', 'seller')
            ->where('created_by_id', $seller->id)
            ->firstOrFail();

        AttributeTerm::create([
            'attribute_id' => $attribute->id,
            'term' => $request->term,

            // ⭐ Seller ownership
            'created_by' => 'seller',
            'created_by_id' => $seller->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Term added & sent for approval.');
    }


    /* =======================
       DELETE TERM
    ======================= */
    public function termsdestroy($attributeId, $termId)
    {
        $seller = auth()->guard('seller')->user();

        $term = AttributeTerm::where('id', $termId)
            ->where('created_by', 'seller')
            ->where('created_by_id', $seller->id)
            ->firstOrFail();

        $term->delete();

        return back()->with('success', 'Term deleted successfully.');
    }
}
