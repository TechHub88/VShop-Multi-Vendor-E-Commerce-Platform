<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Footer;


class FooterController extends Controller
{
     public function index()
    {
        $footers = Footer::orderBy('id', 'DESC')->get();
        return view('admin.footers.index', compact('footers'));
    }

    public function create()
    {
        return view('admin.footers.create');
    }

 public function store(Request $request)
{
    $request->validate([
        'footer_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        // 'social_icons.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        // other footer fields validation...
    ]);

    $footer = new Footer();

    // Handle footer logo
    if ($request->hasFile('footer_logo')) {
        $logoName = time() . '_logo.' . $request->footer_logo->extension();
        $request->footer_logo->move(public_path('uploads/footer'), $logoName);
        $footer->footer_logo = $logoName;
    }

    // Handle social media icons
    $socialPlatforms = ['facebook', 'linkedin', 'youtube', 'instagram'];
    if ($request->hasFile('social_icons')) {
        foreach ($request->file('social_icons') as $index => $icon) {
            if (isset($socialPlatforms[$index])) {
                $iconName = time() . '_icon_' . $index . '.' . $icon->extension();
                $icon->move(public_path('uploads/footer'), $iconName);
                $footer->{$socialPlatforms[$index]} = $iconName;
            }
        }
    }

    // Assign other fields
    $footer->quick_home = $request->quick_home;
    $footer->quick_about = $request->quick_about;
    $footer->quick_faq = $request->quick_faq;
    $footer->quick_testimonials = $request->quick_testimonials;
    $footer->quick_blogs = $request->quick_blogs;

    $footer->company_privacy_policy = $request->company_privacy_policy;
    $footer->company_return_policy = $request->company_return_policy;
    $footer->company_terms = $request->company_terms;

    $footer->category_corporate = $request->category_corporate;
    $footer->category_work = $request->category_work;
    $footer->category_healthcare = $request->category_healthcare;
    $footer->category_school = $request->category_school;

    $footer->address = $request->address;
    $footer->phone = $request->phone;
    $footer->fax = $request->fax;
    $footer->email = $request->email;

    $footer->save();

    return redirect()->route('admin.footers.index')->with('success', 'Footer added successfully.');
}

public function update(Request $request, $id)
{
    $request->validate([
        'footer_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'social_icons.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        // other footer fields validation...
    ]);

    $footer = Footer::findOrFail($id);

    // Update footer logo
    if ($request->hasFile('footer_logo')) {
        // delete old logo
        if ($footer->footer_logo && file_exists(public_path('uploads/footer/' . $footer->footer_logo))) {
            unlink(public_path('uploads/footer/' . $footer->footer_logo));
        }
        $logoName = time() . '_logo.' . $request->footer_logo->extension();
        $request->footer_logo->move(public_path('uploads/footer'), $logoName);
        $footer->footer_logo = $logoName;
    }

    // Update social media icons
    $socialPlatforms = ['facebook', 'linkedin', 'youtube', 'instagram'];
    if ($request->hasFile('social_icons')) {
        foreach ($request->file('social_icons') as $index => $icon) {
            if (isset($socialPlatforms[$index])) {
                // delete old icon if exists
                if ($footer->{$socialPlatforms[$index]} && file_exists(public_path('uploads/footer/' . $footer->{$socialPlatforms[$index]}))) {
                    unlink(public_path('uploads/footer/' . $footer->{$socialPlatforms[$index]}));
                }
                $iconName = time() . '_icon_' . $index . '.' . $icon->extension();
                $icon->move(public_path('uploads/footer'), $iconName);
                $footer->{$socialPlatforms[$index]} = $iconName;
            }
        }
    }

    // Update other fields
    $footer->quick_home = $request->quick_home;
    $footer->quick_about = $request->quick_about;
    $footer->quick_faq = $request->quick_faq;
    $footer->quick_testimonials = $request->quick_testimonials;
    $footer->quick_blogs = $request->quick_blogs;

    $footer->company_privacy_policy = $request->company_privacy_policy;
    $footer->company_return_policy = $request->company_return_policy;
    $footer->company_terms = $request->company_terms;

    $footer->category_corporate = $request->category_corporate;
    $footer->category_work = $request->category_work;
    $footer->category_healthcare = $request->category_healthcare;
    $footer->category_school = $request->category_school;

    $footer->address = $request->address;
    $footer->phone = $request->phone;
    $footer->fax = $request->fax;
    $footer->email = $request->email;

    $footer->save();

    return redirect()->route('admin.footers.index')->with('success', 'Footer updated successfully.');
}


    public function edit($id)
    {
        $footer = Footer::findOrFail($id);
        return view('admin.footers.edit', compact('footer'));
    }

    public function destroy($id)
    {
        Footer::findOrFail($id)->delete();
        return redirect()->route('admin.footers.index')->with('success', 'Footer deleted successfully.');
    }
}
