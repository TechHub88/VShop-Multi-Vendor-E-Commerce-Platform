<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;

class SliderController extends Controller
{
   public function index()
    {
        $sliders = Slider::orderBy('id', 'DESC')->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'banner_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'second_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $slider = new Slider();

        if ($request->hasFile('banner_image')) {
            $bannerName = time() . '_banner.' . $request->banner_image->extension();
            $request->banner_image->move(public_path('uploads/slider'), $bannerName);
            $slider->banner_image = $bannerName;
        }

        if ($request->hasFile('second_image')) {
            $secondName = time() . '_second.' . $request->second_image->extension();
            $request->second_image->move(public_path('uploads/slider'), $secondName);
            $slider->second_image = $secondName;
        }

        $slider->save();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider Added Successfully');
    }

    public function edit($id)
    {
        $slider = Slider::findOrFail($id);
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'second_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $slider = Slider::findOrFail($id);

        if ($request->hasFile('banner_image')) {
            $bannerName = time() . '_banner.' . $request->banner_image->extension();
            $request->banner_image->move(public_path('uploads/slider'), $bannerName);
            $slider->banner_image = $bannerName;
        }

        if ($request->hasFile('second_image')) {
            $secondName = time() . '_second.' . $request->second_image->extension();
            $request->second_image->move(public_path('uploads/slider'), $secondName);
            $slider->second_image = $secondName;
        }

        $slider->save();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider Updated Successfully');
    }

    public function destroy($id)
    {
        Slider::findOrFail($id)->delete();
        return redirect()->route('admin.sliders.index')->with('success', 'Slider Deleted Successfully');
    }
}
