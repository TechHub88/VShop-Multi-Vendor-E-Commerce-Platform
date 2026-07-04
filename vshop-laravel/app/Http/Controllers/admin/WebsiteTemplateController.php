<?php


namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteTemplate;
use Illuminate\Http\Request;

class WebsiteTemplateController extends Controller
{
    public function index()
    {
        $templates = WebsiteTemplate::latest()->get();
        return view('admin.templates.index',compact('templates'));
    }

    public function create()
    {
        return view('admin.templates.create');
    }

public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'application_type' => 'required',
        'preview_image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $imagePath = null;

    if ($request->hasFile('preview_image')) {

        $file = $request->file('preview_image');

        // unique filename
        $filename = time().'_'.$file->getClientOriginalName();

        // move to PUBLIC/templates
        $file->move(public_path('templates'), $filename);

        // save DB path
        $imagePath = 'templates/'.$filename;
    }

    WebsiteTemplate::create([
        'name' => $request->name,
        'application_type' => $request->application_type,
        'preview_image' => $imagePath,
        'description' => $request->description
    ]);

    return redirect()
        ->route('admin.website.templates.index')
        ->with('success','Template Added Successfully');
}
    
    public function edit($id)
{
    $template = WebsiteTemplate::findOrFail($id);

    return view(
        'admin.website_templates.edit',
        compact('template')
    );
}

public function update(Request $request,$id)
{
    $template = WebsiteTemplate::findOrFail($id);

    $template->update([
        'name' => $request->name,
        'application_type' => $request->application_type,
        'description' => $request->description,
        'status' => $request->status,
    ]);

    return redirect()
        ->route('admin.website.templates.index')
        ->with('success','Template updated successfully');
}

public function destroy($id)
{
    $template = WebsiteTemplate::findOrFail($id);

    $template->delete();

    return back()->with('success','Template deleted successfully');
}
}