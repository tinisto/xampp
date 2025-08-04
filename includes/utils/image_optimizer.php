<?php
/**
 * Image Optimization Utilities
 * Provides image compression, resizing, and format conversion
 */

class ImageOptimizer {
    
    /**
     * Optimize image by compressing and resizing
     * @param string $sourcePath Path to source image
     * @param string $destinationPath Path to save optimized image
     * @param array $options Optimization options
     * @return bool Success status
     */
    public static function optimize($sourcePath, $destinationPath, $options = []) {
        $defaults = [
            'quality' => 85,
            'max_width' => 1920,
            'max_height' => 1080,
            'format' => null, // auto-detect or specify 'jpg', 'png', 'webp'
            'progressive' => true,
            'strip_metadata' => true
        ];
        
        $settings = array_merge($defaults, $options);
        
        if (!file_exists($sourcePath)) {
            return false;
        }
        
        // Get image info
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }
        
        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];
        $sourceMime = $imageInfo['mime'];
        
        // Create source image resource
        $sourceImage = self::createImageFromFile($sourcePath, $sourceMime);
        if (!$sourceImage) {
            return false;
        }
        
        // Calculate new dimensions
        $dimensions = self::calculateDimensions(
            $sourceWidth, 
            $sourceHeight, 
            $settings['max_width'], 
            $settings['max_height']
        );
        
        // Create destination image
        $destImage = imagecreatetruecolor($dimensions['width'], $dimensions['height']);
        
        // Preserve transparency for PNG
        if ($sourceMime === 'image/png') {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 0, 0, 0, 127);
            imagefill($destImage, 0, 0, $transparent);
        }
        
        // Resize image
        imagecopyresampled(
            $destImage, $sourceImage,
            0, 0, 0, 0,
            $dimensions['width'], $dimensions['height'],
            $sourceWidth, $sourceHeight
        );
        
        // Determine output format
        $outputFormat = $settings['format'] ?: self::detectOutputFormat($sourceMime);
        
        // Save optimized image
        $success = self::saveImage($destImage, $destinationPath, $outputFormat, $settings);
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($destImage);
        
        return $success;
    }
    
    /**
     * Create image resource from file
     */
    private static function createImageFromFile($path, $mime) {
        switch ($mime) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/webp':
                return function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false;
            default:
                return false;
        }
    }
    
    /**
     * Calculate optimal dimensions while maintaining aspect ratio
     */
    private static function calculateDimensions($sourceWidth, $sourceHeight, $maxWidth, $maxHeight) {
        if ($sourceWidth <= $maxWidth && $sourceHeight <= $maxHeight) {
            return ['width' => $sourceWidth, 'height' => $sourceHeight];
        }
        
        $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight);
        
        return [
            'width' => round($sourceWidth * $ratio),
            'height' => round($sourceHeight * $ratio)
        ];
    }
    
    /**
     * Detect optimal output format
     */
    private static function detectOutputFormat($sourceMime) {
        // Prefer WebP if supported, otherwise JPEG for photos, PNG for graphics
        if (function_exists('imagewebp')) {
            return 'webp';
        }
        
        return $sourceMime === 'image/png' ? 'png' : 'jpg';
    }
    
    /**
     * Save image with specified format and settings
     */
    private static function saveImage($image, $path, $format, $settings) {
        // Create directory if it doesn't exist
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        switch ($format) {
            case 'jpg':
            case 'jpeg':
                if ($settings['progressive']) {
                    imageinterlace($image, 1);
                }
                return imagejpeg($image, $path, $settings['quality']);
                
            case 'png':
                // PNG quality is 0-9, convert from 0-100
                $pngQuality = 9 - round(($settings['quality'] / 100) * 9);
                return imagepng($image, $path, $pngQuality);
                
            case 'webp':
                if (function_exists('imagewebp')) {
                    return imagewebp($image, $path, $settings['quality']);
                }
                return false;
                
            case 'gif':
                return imagegif($image, $path);
                
            default:
                return false;
        }
    }
    
    /**
     * Generate responsive image variants
     * @param string $sourcePath Path to source image
     * @param string $baseOutputPath Base path for output images
     * @param array $sizes Array of size configurations
     * @return array Generated file paths
     */
    public static function generateResponsiveImages($sourcePath, $baseOutputPath, $sizes = []) {
        $defaultSizes = [
            'small' => ['width' => 480, 'suffix' => '_480w'],
            'medium' => ['width' => 768, 'suffix' => '_768w'],
            'large' => ['width' => 1200, 'suffix' => '_1200w'],
            'xlarge' => ['width' => 1920, 'suffix' => '_1920w']
        ];
        
        $sizes = $sizes ?: $defaultSizes;
        $generatedFiles = [];
        
        $pathInfo = pathinfo($baseOutputPath);
        
        foreach ($sizes as $sizeName => $config) {
            $outputPath = $pathInfo['dirname'] . '/' . 
                         $pathInfo['filename'] . 
                         $config['suffix'] . '.' . 
                         $pathInfo['extension'];
            
            $options = [
                'max_width' => $config['width'],
                'max_height' => $config['height'] ?? $config['width'] * 2, // Allow tall images
                'quality' => $config['quality'] ?? 85
            ];
            
            if (self::optimize($sourcePath, $outputPath, $options)) {
                $generatedFiles[$sizeName] = $outputPath;
            }
        }
        
        return $generatedFiles;
    }
    
    /**
     * Get image dimensions without loading the full image
     * @param string $path Image file path
     * @return array|false Dimensions array or false on failure
     */
    public static function getImageDimensions($path) {
        if (!file_exists($path)) {
            return false;
        }
        
        $info = getimagesize($path);
        if (!$info) {
            return false;
        }
        
        return [
            'width' => $info[0],
            'height' => $info[1],
            'mime' => $info['mime'],
            'ratio' => $info[0] / $info[1]
        ];
    }
    
    /**
     * Convert image to WebP format
     * @param string $sourcePath Path to source image
     * @param string $webpPath Path to save WebP image
     * @param int $quality WebP quality (0-100)
     * @return bool Success status
     */
    public static function convertToWebP($sourcePath, $webpPath, $quality = 85) {
        if (!function_exists('imagewebp')) {
            return false;
        }
        
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }
        
        $sourceImage = self::createImageFromFile($sourcePath, $imageInfo['mime']);
        if (!$sourceImage) {
            return false;
        }
        
        // Create directory if needed
        $dir = dirname($webpPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $success = imagewebp($sourceImage, $webpPath, $quality);
        imagedestroy($sourceImage);
        
        return $success;
    }
    
    /**
     * Generate srcset string for responsive images
     * @param string $basePath Base image path
     * @param array $variants Array of image variants
     * @return string Srcset string
     */
    public static function generateSrcset($basePath, $variants) {
        $srcsetParts = [];
        
        foreach ($variants as $variant) {
            $width = $variant['width'];
            $path = $variant['path'] ?? str_replace('.', "_{$width}w.", $basePath);
            $srcsetParts[] = "{$path} {$width}w";
        }
        
        return implode(', ', $srcsetParts);
    }
    
    /**
     * Compress existing image in place
     * @param string $imagePath Path to image to compress
     * @param int $quality Compression quality
     * @return bool Success status
     */
    public static function compress($imagePath, $quality = 85) {
        $tempPath = $imagePath . '.tmp';
        
        if (self::optimize($imagePath, $tempPath, ['quality' => $quality])) {
            return rename($tempPath, $imagePath);
        }
        
        return false;
    }
    
    /**
     * Get file size reduction information
     * @param string $originalPath Original image path
     * @param string $optimizedPath Optimized image path
     * @return array Size reduction stats
     */
    public static function getSizeReduction($originalPath, $optimizedPath) {
        if (!file_exists($originalPath) || !file_exists($optimizedPath)) {
            return false;
        }
        
        $originalSize = filesize($originalPath);
        $optimizedSize = filesize($optimizedPath);
        $reduction = $originalSize - $optimizedSize;
        $percentage = round(($reduction / $originalSize) * 100, 2);
        
        return [
            'original_size' => $originalSize,
            'optimized_size' => $optimizedSize,
            'reduction_bytes' => $reduction,
            'reduction_percentage' => $percentage,
            'original_size_formatted' => self::formatBytes($originalSize),
            'optimized_size_formatted' => self::formatBytes($optimizedSize)
        ];
    }
    
    /**
     * Format bytes to human readable format
     */
    private static function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}