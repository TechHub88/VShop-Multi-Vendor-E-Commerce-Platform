<?php

if (!function_exists('getCurrencySymbol')) {
    function getCurrencySymbol($currency) {
        return match($currency) {
            'INR' => '₹',
            'EUR' => '€',
            'GBP' => '£',
            default => '$',
        };
    }
}

if (!function_exists('uploadToR2')) {
    function uploadToR2($file, $folder, $filename = null) {
        if (!$file) return null;
        if (!$filename) {
            $filename = \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension();
        }
        $path = rtrim($folder, '/') . '/' . $filename;
        \Illuminate\Support\Facades\Storage::disk('r2')->putFileAs($folder, $file, $filename, 'public');
        return \Illuminate\Support\Facades\Storage::disk('r2')->url($path);
    }
}

if (!function_exists('deleteFromR2')) {
    function deleteFromR2($url) {
        if (empty($url)) return;
        $path = ltrim(parse_url($url, PHP_URL_PATH), '/');
        \Illuminate\Support\Facades\Storage::disk('r2')->delete($path);
    }
}
