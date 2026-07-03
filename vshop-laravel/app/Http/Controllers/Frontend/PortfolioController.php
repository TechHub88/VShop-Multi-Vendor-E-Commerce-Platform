<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\About;
use App\Models\Category;
use App\Models\Footer;
class PortfolioController extends Controller
{
        public function schooluniform()
    {
         // About, categories, footer
    $about = About::first();
    $categories = Category::all();
    $footer = Footer::first();
        return view('frontend.portfolio.schooluniform',compact('about','categories','footer'));
    }
     public function hospitaluniform()
    {
         // About, categories, footer
    $about = About::first();
    $categories = Category::all();
    $footer = Footer::first();
        return view('frontend.portfolio.hospitaluniform',compact('about','categories','footer'));
    }
    
    
    
      public function hospitalityuniform()
    {
         // About, categories, footer
    $about = About::first();
    $categories = Category::all();
    $footer = Footer::first();
        return view('frontend.portfolio.hospitalityuniform',compact('about','categories','footer'));
    }
    
      public function corporateuniform()
    {
         // About, categories, footer
    $about = About::first();
    $categories = Category::all();
    $footer = Footer::first();
        return view('frontend.portfolio.corporateuniform',compact('about','categories','footer'));
    }
    
 
 
 

}
