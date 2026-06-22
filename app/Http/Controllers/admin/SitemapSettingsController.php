<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Sitemap\SitemapGenerator;

class SitemapSettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.sitemap');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'domain' => 'required|url',
            'filename' => 'required|string',
            'extra_urls' => 'nullable|string',
        ]);

        $domain = $request->domain;
        $filename = trim($request->filename);
        $extraUrls = array_filter(explode("\n", $request->extra_urls ?? ''));

        // generate sitemap
        $path = public_path($filename);
        SitemapGenerator::create($domain)
            ->getSitemap()
            ->writeToFile($path);

        // if extra URLs specified, you can append them
        if (! empty($extraUrls)) {
            $sitemap = \Spatie\Sitemap\Sitemap::create()->add(...$extraUrls);
            $sitemap->writeToFile($path);
        }

        return redirect()->route('admin.settings.sitemap')->with('success', 'Sitemap generated successfully at '.$filename);
    }
}
