<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;   // ← correct
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;  
class SubcategoryController extends Controller
{
    // --------------------------
    // LISTING
    // --------------------------
    public function index()
    {
        $subcategories = SubCategory::with('category')->paginate(15);
        return view('admin.subcategories.index', compact('subcategories'));
    }

    // --------------------------
    // CREATE FORM
    // --------------------------
    public function create()
    {
        $categories = Category::all(); // For dropdown
        return view('admin.subcategories.create', compact('categories'));
    }

    // --------------------------
    // STORE DATA
    // --------------------------
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
        ]);

        SubCategory::create([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
        ]);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategory created successfully');
    }

    // --------------------------
    // EDIT FORM
    // --------------------------
    public function edit($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $categories = Category::all();
        return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }

    // --------------------------
    // UPDATE DATA
    // --------------------------
    public function update(Request $request, $id)
    {
        $subcategory = SubCategory::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
        ]);

        $subcategory->update([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
        ]);

        return redirect()->route('subcategories.index')
            ->with('success', 'Subcategory updated successfully');
    }

    // --------------------------
    // DELETE
    // --------------------------
    public function destroy($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $subcategory->delete();

        return redirect()->back()->with('success', 'Subcategory deleted successfully');
    }
}
