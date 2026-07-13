<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageModeratorService
{
    /**
     * Check if an image contains NSFW content.
     * Returns true if the image is safe, false if it contains NSFW content.
     */
    public static function isSafeImage($imagePath): bool
    {
        // ------------------ DEVELOPMENT MOCK FOR TESTING ------------------
        if ($imagePath) {
            $filename = strtolower(basename($imagePath));
            if (str_contains($filename, 'nsfw') || 
                str_contains($filename, 'vulgar') || 
                str_contains($filename, 'nudity') || 
                str_contains($filename, 'fraud') || 
                str_contains($filename, 'naked') ||
                str_contains($filename, 'inappropriate')) {
                Log::warning("Mock NSFW check: Blocked image due to filename containing trigger word: {$filename}");
                return false;
            }
        }

        // For development/XAMPP environment, we can check if credentials exist.
        // If they don't, we can log and fallback to true, or use local microservice if configured.
        return self::checkViaSightengine($imagePath);
    }

    /**
     * Check if a video URL contains NSFW content (Mock implementation).
     * Returns true if the URL is safe, false if it contains NSFW keywords.
     */
    public static function isSafeVideoURL($url): bool
    {
        if (!$url) return true;
        
        $lowercaseUrl = strtolower($url);
        
        // List of trigger words and blocked domains
        $blockedKeywords = [
            'nsfw', 'vulgar', 'nudity', 'naked', 'porn', 'xxx', 'sex', 
            'pornhub', 'xvideos', 'xnxx', 'redtube', 'youporn'
        ];
        
        foreach ($blockedKeywords as $keyword) {
            if (str_contains($lowercaseUrl, $keyword)) {
                Log::warning("Blocked video URL due to NSFW keyword '{$keyword}': {$url}");
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check via Sightengine API
     */
    private static function checkViaSightengine($imagePath): bool
    {
        try {
            $apiUser = env('SIGHTENGINE_API_USER');
            $apiKey = env('SIGHTENGINE_API_KEY');

            // If no credentials, log a warning and let it pass for development
            if (!$apiUser || !$apiKey) {
                Log::info('Sightengine credentials not set in .env. Skipping NSFW image check.');
                return true;
            }

            if (!file_exists($imagePath)) {
                Log::error("Image file not found for NSFW check: {$imagePath}");
                return true;
            }

            $response = Http::attach(
                'media',
                file_get_contents($imagePath),
                basename($imagePath)
            )->post('https://api.sightengine.com/1.0/check.json', [
                'models' => 'nudity-2.0,wad,scam',
                'api_user' => $apiUser,
                'api_key' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Nudity score check (scores range from 0 to 1)
                $nudity = $data['nudity'] ?? null;
                if ($nudity) {
                    if (($nudity['sexual_activity'] ?? 0) > 0.30 || 
                        ($nudity['sexual_display'] ?? 0) > 0.30 ||
                        ($nudity['erotica'] ?? 0) > 0.40) {
                        Log::warning("NSFW Image blocked (Sightengine): " . json_encode($nudity));
                        return false;
                    }
                }
                
                // Optional: check weapon, alcohol, drugs (wad)
                $wad = $data['weapon_alcohol_drugs'] ?? null;
                if ($wad) {
                    if (($wad['weapon'] ?? 0) > 0.50 || ($wad['drugs'] ?? 0) > 0.50) {
                        Log::warning("Weapons/Drugs detected and blocked: " . json_encode($wad));
                        return false;
                    }
                }

                return true;
            }
        } catch (\Exception $e) {
            Log::error('NSFW Check Error (Sightengine): ' . $e->getMessage());
        }

        return true;
    }
}
