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
        
        // Compress if it is an image
        $mime = $file->getClientMimeType();
        if (str_starts_with($mime, 'image/') && !in_array($mime, ['image/svg+xml', 'image/gif'])) {
            try {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($file->getRealPath());
                
                $ext = strtolower($file->getClientOriginalExtension());
                if ($ext === 'webp') {
                    $encoded = $image->toWebp(75);
                } elseif ($ext === 'png') {
                    $encoded = $image->toPng();
                } else {
                    $encoded = $image->toJpeg(75);
                }
                
                \Illuminate\Support\Facades\Storage::disk('r2')->put($path, (string) $encoded, 'public');
                return \Illuminate\Support\Facades\Storage::disk('r2')->url($path);
            } catch (\Exception $e) {
                // Fallback to default if compression fails
            }
        }

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
