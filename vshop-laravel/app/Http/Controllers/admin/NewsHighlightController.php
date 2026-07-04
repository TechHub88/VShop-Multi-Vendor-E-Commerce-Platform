<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsHighlight;


class NewsHighlightController extends Controller
{
     public function index()
    {
        $news = NewsHighlight::orderBy('id', 'DESC')->get();
        return view('admin.news_highlights.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news_highlights.create');
    }

   public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $news = new NewsHighlight();
    $news->title = $request->title;
    $news->description = $request->description;

    $imageNames = [];
    if($request->hasFile('images')){
        foreach($request->file('images') as $image){
            $name = time().'_news_'.uniqid().'.'.$image->extension();
            $image->move(public_path('uploads/news'), $name);
            $imageNames[] = $name;
        }
    }

    $news->image = json_encode($imageNames); // Store as JSON
    $news->save();

    return redirect()->route('admin.newshighlights.index')->with('success', 'News Highlight added successfully.');
}

public function update(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $news = NewsHighlight::findOrFail($id);
    $news->title = $request->title;
    $news->description = $request->description;

    // Existing images
    $existingImages = json_decode($news->image, true) ?? [];

    if($request->hasFile('images')){
        // Delete old images if needed
        foreach($existingImages as $oldImage){
            if(file_exists(public_path('uploads/news/'.$oldImage))){
                unlink(public_path('uploads/news/'.$oldImage));
            }
        }

        $newImages = [];
        foreach($request->file('images') as $image){
            $name = time().'_news_'.uniqid().'.'.$image->extension();
            $image->move(public_path('uploads/news'), $name);
            $newImages[] = $name;
        }

        $news->image = json_encode($newImages);
    }

    $news->save();

    return redirect()->route('admin.newshighlights.index')->with('success', 'News Highlight updated successfully.');
}


    public function destroy($id)
    {
        $news = NewsHighlight::findOrFail($id);
        if($news->image && file_exists(public_path('uploads/news/'.$news->image))){
            unlink(public_path('uploads/news/'.$news->image));
        }
        $news->delete();

        return redirect()->route('admin.newshighlights.index')->with('success', 'News Highlight deleted successfully.');
    }
}
