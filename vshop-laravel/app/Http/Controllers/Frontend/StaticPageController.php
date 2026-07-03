<?php

namespace App\Http\Controllers\Frontend;
use App\Models\About;
use App\Models\Category;
use App\Models\Footer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function ourServices()
{
      $about = About::first();
    $categories = Category::all();
    $footer = Footer::first();
    return view('frontend.ourservices',compact('about','categories','footer'));
}


 public function aboutus()
    {
         $about = About::first();
    $categories = Category::all();
    $footer = Footer::first();
        return view('frontend.staticpages.about',compact('about','categories','footer'));
    }

    // public function services()
    // {
    //     return view('pages.services');
    // }

    public function contact()
    {
        $about = About::first();
    $categories = Category::all();
    $footer = Footer::first();
        return view('frontend.staticpages.contact',compact('about','categories','footer'));
    }
}
