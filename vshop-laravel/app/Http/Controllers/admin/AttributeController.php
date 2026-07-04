<?php

namespace App\Http\Controllers\admin;
use App\Models\AttributeTerm; //
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
     /**
     * Display a listing of attributes
     */
    public function index()
    {
        $attributes = Attribute::all();
        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Store a new attribute and optional terms
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'terms' => 'nullable|array',
            'terms.*' => 'required|string|max:255',
        ]);

        // Create attribute
        $attribute = Attribute::create([
            'name' => $request->name,
        ]);

        // Create terms if provided
        if ($request->has('terms') && is_array($request->terms)) {
            foreach ($request->terms as $term) {
                AttributeTerm::create([
                    'attribute_id' => $attribute->id,
                    'term' => $term,
                ]);
            }
        }

        return redirect()->route('admin.attributes.index')
                         ->with('success', 'Attribute Added Successfully');
    }

    /**
     * Delete an attribute along with its terms
     */
    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->delete(); // Cascade deletes terms due to FK ON DELETE CASCADE
        return back()->with('success', 'Attribute Deleted Successfully');
    }

    /**
     * Show terms for a given attribute
     */
    public function terms($id)
    {
        $attribute = Attribute::with('terms')->find($id);

        if (!$attribute) {
            return view('admin.attributes.terms', ['attribute' => null]);
        }

        return view('admin.attributes.terms', compact('attribute'));
    }

    /**
     * Store a new term for a specific attribute
     */
    public function termsStore(Request $request, $id)
    {
        $request->validate([
            'term' => 'required|string|max:255',
        ]);

        $attribute = Attribute::findOrFail($id);

        AttributeTerm::create([
            'attribute_id' => $attribute->id,
            'term' => $request->term,
        ]);

         return redirect()->route('admin.attributes.index')
                         ->with('success', 'Terms Added Successfully');
    }

    /**
     * Delete a specific term
     */


    public function edit($attributeId, $termId)
{
    $attribute = Attribute::findOrFail($attributeId);
    $term = AttributeTerm::findOrFail($termId);

    return view('admin.attributes.editterm', compact('attribute', 'term'));
}

public function update(Request $request, $attributeId, $termId)
{
    $request->validate([
        'term' => 'required|string|max:255',
    ]);

    $term = AttributeTerm::findOrFail($termId);
    $term->term = $request->term;
    $term->save();

    return redirect()->route('admin.attributes.terms', $attributeId)->with('success', 'Term updated successfully');
}

public function termsdestroy($attributeId, $termId)
{
    // Find the term by ID
    $term = AttributeTerm::findOrFail($termId);

    // Delete the term
    $term->delete();

    // Redirect back with a success message
    return redirect()->route('admin.attributes.terms', $attributeId)->with('success', 'Term deleted successfully');
}


}
