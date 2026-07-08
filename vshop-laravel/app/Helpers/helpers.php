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
        // Prevent 502 Bad Gateway due to memory limits or timeouts during heavy uploads
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');
        
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
                
                // Write to a temporary file locally so putFileAs can detect the correct MIME type
                $tempPath = sys_get_temp_dir() . '/' . $filename;
                file_put_contents($tempPath, (string) $encoded);
                
                \Illuminate\Support\Facades\Storage::disk('r2')->putFileAs($folder, new \Illuminate\Http\File($tempPath), $filename, 'public');
                @unlink($tempPath);
                
                return \Illuminate\Support\Facades\Storage::disk('r2')->url($path);
            } catch (\Throwable $e) {
                // If compression fails (e.g. Memory Exhaustion), gracefully fallback to normal upload
                \Illuminate\Support\Facades\Log::error('Image Compression Failed: ' . $e->getMessage());
            }
        }

        try {
            $path = \Illuminate\Support\Facades\Storage::disk('r2')->putFileAs($folder, $file, $filename, 'public');
            return \Illuminate\Support\Facades\Storage::disk('r2')->url($path);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Upload to R2 Failed: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('deleteFromR2')) {
    function deleteFromR2($url) {
        if (empty($url)) return;
        $path = ltrim(parse_url($url, PHP_URL_PATH), '/');
        \Illuminate\Support\Facades\Storage::disk('r2')->delete($path);
    }
}
