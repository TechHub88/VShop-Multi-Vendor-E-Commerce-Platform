<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\About;

class AboutSectionController extends Controller
{
     public function index()
    {
        $aboutSections = About::orderBy('id', 'DESC')->get();
        return view('admin.about.index', compact('aboutSections'));
    }

    public function create()
    {
        return view('admin.about.create');
    }

  public function store(Request $request)
{
    $request->validate([
        'about1' => 'nullable|string|max:255',
        'about2' => 'nullable|string|max:255',
        'about3' => 'nullable|string|max:255',
        'about4' => 'nullable|string|max:255',
        'about5' => 'nullable|string|max:255',
       
    ]);

    $about = new About();
    $about->about1 = $request->about1;
    $about->about2 = $request->about2;
    $about->about3 = $request->about3;
    $about->about4 = $request->about4;
    $about->about5 = $request->about5;
      $about->about6 = $request->about6;
        $about->about7 = $request->about7;
          $about->about8 = $request->about8;
            $about->about9 = $request->about9;
             
                $about->about10 = $request->about10;
    $about->description = $request->description;

    if($request->hasFile('bgimage')){
        $imageName = time() . '.' . $request->bgimage->extension();
        $request->bgimage->move(public_path('uploads/about'), $imageName);
        $about->bgimage = $imageName;
    }

    $about->save();

    return redirect()->route('admin.about.index')->with('success', 'About Section Added Successfully');
}


    public function edit($id)
    {
        $about = About::findOrFail($id);
        return view('admin.about.edit', compact('about'));
    }

 public function update(Request $request, $id)
{
    // $request->validate([
    //     'about1' => 'nullable|string|max:255',
    //     'about2' => 'nullable|string|max:255',
    //     'about3' => 'nullable|string|max:255',
    //     'about4' => 'nullable|string|max:255',
    //     'about5' => 'nullable|string|max:255',
    //     'description' => 'nullable|string',
    //     'bgimage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    // ]);

    $about = About::findOrFail($id);

    $about->about1 = $request->about1;
    $about->about2 = $request->about2;
    $about->about3 = $request->about3;
    $about->about4 = $request->about4;
    $about->about5 = $request->about5;
      $about->about6 = $request->about6;
        $about->about7 = $request->about7;
          $about->about8 = $request->about8;
            $about->about9 = $request->about9;
             
                $about->about10 = $request->about10;
    $about->description = $request->description;

    if($request->hasFile('bgimage')){
        $imageName = time() . '.' . $request->bgimage->extension();
        $request->bgimage->move(public_path('uploads/about'), $imageName);
        $about->bgimage = $imageName;
    }

    $about->save();

    return redirect()->route('admin.about.index')->with('success', 'About Section Updated Successfully');
}

    public function destroy($id)
    {
        About::findOrFail($id)->delete();
        return redirect()->route('admin.about.index')->with('success', 'About section deleted.');
    }
}
