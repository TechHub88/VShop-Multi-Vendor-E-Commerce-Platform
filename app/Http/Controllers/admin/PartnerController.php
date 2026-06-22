<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partner;
use App\Models\PartnersContent;

class PartnerController extends Controller
{
   
    public function index()
    {
        $partners = Partner::orderBy('id','DESC')->get();
        $contents = PartnersContent::orderBy('id','DESC')->get();
        return view('admin.partners.index', compact('partners','contents'));
    }

    public function create()
    {
        return view('admin.partners.create'); // Create form for partner and content separately if needed
    }

public function storePartner(Request $request)
{
    // $request->validate([
    //     'partner_logo.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    //     'partner_name.*' => 'nullable|string|max:255',
    // ]);

    $logos = $request->file('partner_logo');
    $names = $request->partner_name;

    if($logos){
        foreach($logos as $index => $logo){
            $partner = new Partner();
            $name = time().'_logo_'.uniqid().'.'.$logo->extension();
            $logo->move(public_path('uploads/partners'), $name);
            $partner->partner_logo = $name;
            $partner->partner_name = $names[$index] ?? null;
            $partner->save();
        }
    }

    return redirect()->route('admin.partners.index')->with('success', 'Partner(s) added successfully.');
}


    public function editPartner($id)
    {
        $partner = Partner::findOrFail($id);
        return view('admin.partners.edit', compact('partner'));
    }
public function updatePartner(Request $request, $id)
{
    $request->validate([
        'partner_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'partner_name' => 'nullable|string|max:255',
    ]);

    $partner = Partner::findOrFail($id);
    $partner->partner_name = $request->partner_name;

    if($request->hasFile('partner_logo')){
        // Delete old logo if exists
        if($partner->partner_logo && file_exists(public_path('uploads/partners/'.$partner->partner_logo))){
            unlink(public_path('uploads/partners/'.$partner->partner_logo));
        }

        $name = time().'_logo_'.uniqid().'.'.$request->partner_logo->extension();
        $request->partner_logo->move(public_path('uploads/partners'), $name);
        $partner->partner_logo = $name;
    }

    $partner->save();

    return redirect()->route('admin.partners.index')->with('success', 'Partner updated successfully.');
}

    public function destroyPartner($id)
    {
        $partner = Partner::findOrFail($id);
        if($partner->partner_logo && file_exists(public_path('uploads/partners/'.$partner->partner_logo))){
            unlink(public_path('uploads/partners/'.$partner->partner_logo));
        }
        $partner->delete();

        return redirect()->route('admin.partners.index')->with('success', 'Partner deleted successfully.');
    }

    // ------------------- PARTNERS CONTENT CRUD -------------------

    public function createContent()
    {
        return view('admin.partners_content.create');
    }

    public function storeContent(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        PartnersContent::create($request->only('title','tagline','description'));

        return redirect()->route('admin.partners.index')->with('success', 'Partners Content added successfully.');
    }

    public function editContent($id)
    {
        $content = PartnersContent::findOrFail($id);
        return view('admin.partners_content.edit', compact('content'));
    }

    public function updateContent(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $content = PartnersContent::findOrFail($id);
        $content->update($request->only('title','tagline','description'));

        return redirect()->route('admin.partners.index')->with('success', 'Partners Content updated successfully.');
    }

    public function destroyContent($id)
    {
        PartnersContent::findOrFail($id)->delete();

        return redirect()->route('admin.partners.index')->with('success', 'Partners Content deleted successfully.');
    }
}
