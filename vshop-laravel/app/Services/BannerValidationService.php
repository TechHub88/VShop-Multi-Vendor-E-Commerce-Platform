<?php
// app/Services/BannerValidationService.php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Http\UploadedFile;
use App\Models\SellerBanner;
use App\Models\RejectedBanner;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;

class BannerValidationService
{
    protected $errors = [];
    protected $warnings = [];
    
    // Configuration
    protected $maxSize = 5 * 1024 * 1024; // 5MB
    protected $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
    protected $allowedExtensions = ['jpg', 'jpeg', 'png'];
    protected $minResolution = [600, 400]; // min width x height
    protected $minAspectRatio = 1.5; // 3:2 minimum
    protected $maxAspectRatio = 2.5; // 5:2 maximum
    protected $dailyUploadLimit = 20;
    
    // Blacklisted keywords
    protected $blacklistedKeywords = [
        'casino', 'gambling', 'adult', 'xxx', 'viagra', 
        'illegal', 'counterfeit', 'weapon', 'drugs'
    ];
    
    public function validate(UploadedFile $file, $sellerId, $checkPreviousRejection = true)
    {
        $this->errors = [];
        
        // 1. MIME Validation
        if (!$this->validateMime($file)) {
            $this->errors[] = 'Invalid file type. Only JPG, JPEG, PNG allowed.';
            return false;
        }
        
        // 2. Extension Validation
        if (!$this->validateExtension($file)) {
            $this->errors[] = 'Invalid file extension. Only .jpg, .jpeg, .png allowed.';
            return false;
        }
        
        // 3. Empty File Validation
        if ($file->getSize() === 0) {
            $this->errors[] = 'File is empty.';
            return false;
        }
        
        // 4. Maximum Size Validation
        if (!$this->validateMaxSize($file)) {
            $this->errors[] = 'File size exceeds ' . ($this->maxSize / 1024 / 1024) . 'MB limit.';
            return false;
        }
        
        // 5. Resolution Validation
        $dimensions = getimagesize($file->getPathname());
        if (!$this->validateResolution($dimensions)) {
            $this->errors[] = "Image resolution must be at least {$this->minResolution[0]}x{$this->minResolution[1]} pixels.";
            return false;
        }
        
        // 6. Aspect Ratio Validation
        if (!$this->validateAspectRatio($dimensions[0], $dimensions[1])) {
            $this->errors[] = "Image aspect ratio must be between {$this->minAspectRatio} and {$this->maxAspectRatio}.";
            return false;
        }
        
        // 7. Duplicate Hash Validation
        $fileHash = md5_file($file->getPathname());
        if ($this->isDuplicateHash($fileHash, $sellerId)) {
            $this->errors[] = 'Duplicate image detected. This image has been uploaded before.';
            return false;
        }
        
        // 8. Previously Rejected Duplicate Detection
        if ($checkPreviousRejection && $this->wasPreviouslyRejected($fileHash, $sellerId)) {
            $this->errors[] = 'This image was previously rejected and cannot be re-uploaded.';
            return false;
        }
        
        // 9. Corrupted Image Validation
        if (!$this->validateImageIntegrity($file)) {
            $this->errors[] = 'Image file appears to be corrupted.';
            return false;
        }
        
        // 10. OCR Content Validation
        $ocrResult = $this->extractTextViaOCR($file);
        if (!$this->validateOCRContent($ocrResult)) {
            return false;
        }
        
        // 11. Watermark Detection
        if ($this->detectWatermark($file)) {
            $this->warnings[] = 'Potential watermark detected. Banner may require review.';
        }
        
        return true;
    }
    
    protected function validateMime(UploadedFile $file)
    {
        return in_array($file->getMimeType(), $this->allowedMimes);
    }
    
    protected function validateExtension(UploadedFile $file)
    {
        return in_array(strtolower($file->getClientOriginalExtension()), $this->allowedExtensions);
    }
    
    protected function validateMaxSize(UploadedFile $file)
    {
        return $file->getSize() <= $this->maxSize;
    }
    
    protected function validateResolution($dimensions)
    {
        return $dimensions[0] >= $this->minResolution[0] && 
               $dimensions[1] >= $this->minResolution[1];
    }
    
    protected function validateAspectRatio($width, $height)
    {
        $ratio = $width / $height;
        return $ratio >= $this->minAspectRatio && $ratio <= $this->maxAspectRatio;
    }
    
    protected function isDuplicateHash($hash, $sellerId)
    {
        return SellerBanner::where('seller_id', $sellerId)
            ->whereJsonContains('banner_image_hash', $hash)
            ->exists();
    }
    
    protected function wasPreviouslyRejected($hash, $sellerId)
    {
        return RejectedBanner::where('seller_id', $sellerId)
            ->where('file_hash', $hash)
            ->exists();
    }
    
    protected function validateImageIntegrity(UploadedFile $file)
    {
        try {
            $image = Image::make($file->getPathname());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    protected function extractTextViaOCR(UploadedFile $file)
    {
        try {
            // Save temporarily
            $tempPath = storage_path('app/temp/' . uniqid() . '.jpg');
            $image = Image::make($file)->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->save($tempPath, 80);
            
            // Extract text using Tesseract OCR
            $ocr = new TesseractOCR($tempPath);
            $text = $ocr->run();
            
            // Clean up
            @unlink($tempPath);
            
            return strtolower($text);
        } catch (\Exception $e) {
            return '';
        }
    }
    
    protected function validateOCRContent($text)
    {
        // Phone Number Detection
        $phonePattern = '/(\+?[0-9]{1,3}[-.\s]?)?[0-9]{10,14}/';
        if (preg_match($phonePattern, $text, $matches)) {
            $this->errors[] = 'Phone numbers are not allowed in banner images. Found: ' . $matches[0];
            return false;
        }
        
        // WhatsApp Detection
        if (preg_match('/whatsapp/i', $text) || preg_match('/wa\.me/i', $text)) {
            $this->errors[] = 'WhatsApp references are not allowed in banner images.';
            return false;
        }
        
        // Telegram Detection
        if (preg_match('/telegram|t\.me/i', $text)) {
            $this->errors[] = 'Telegram references are not allowed in banner images.';
            return false;
        }
        
        // Website URL Detection
        $urlPattern = '/(https?:\/\/[^\s]+|www\.[^\s]+|[a-zA-Z0-9-]+\.[a-zA-Z]{2,})/';
        if (preg_match($urlPattern, $text, $matches)) {
            $this->errors[] = 'Website URLs are not allowed in banner images. Found: ' . $matches[0];
            return false;
        }
        
        // Blacklisted Keywords Detection
        foreach ($this->blacklistedKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                $this->errors[] = "Blacklisted keyword '{$keyword}' detected in banner.";
                return false;
            }
        }
        
        return true;
    }
    
    protected function detectWatermark(UploadedFile $file)
    {
        try {
            $image = Image::make($file);
            
            // Check for semi-transparent areas (common in watermarks)
            $width = $image->width();
            $height = $image->height();
            
            // Sample edges for repeating patterns
            $edges = [
                $image->pickColor(10, 10, 'array'), // top-left
                $image->pickColor($width-10, 10, 'array'), // top-right
                $image->pickColor(10, $height-10, 'array'), // bottom-left
                $image->pickColor($width-10, $height-10, 'array'), // bottom-right
                $image->pickColor($width/2, $height/2, 'array'), // center
            ];
            
            // Check for uniform transparency (simplified watermark detection)
            $hasTransparency = false;
            foreach ($edges as $color) {
                if (isset($color[3]) && $color[3] < 100) { // Alpha channel < 100
                    $hasTransparency = true;
                    break;
                }
            }
            
            return $hasTransparency;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function removeEXIFMetadata(UploadedFile $file, $outputPath)
    {
        try {
            $image = Image::make($file);
            
            // Strip all EXIF data by re-encoding
            $image->save($outputPath, 90);
            
            return true;
        } catch (\Exception $e) {
            // If fails, copy original
            copy($file->getPathname(), $outputPath);
            return false;
        }
    }
    
    public function validateSellerDailyLimit($sellerId)
    {
        $todayUploads = SellerBanner::where('seller_id', $sellerId)
            ->whereDate('created_at', today())
            ->count();
            
        if ($todayUploads >= $this->dailyUploadLimit) {
            $this->errors[] = "Daily upload limit of {$this->dailyUploadLimit} banners reached.";
            return false;
        }
        
        return true;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function getWarnings()
    {
        return $this->warnings;
    }
}