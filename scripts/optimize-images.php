<?php
// Image optimization script

$rootDir = dirname(__DIR__);
$imageDir = $rootDir . '/images/posts-images/';
$optimizedDir = $rootDir . '/images/posts-images/optimized/';

// Create optimized directory if not exists
if (!is_dir($optimizedDir)) {
    mkdir($optimizedDir, 0755, true);
}

function optimizeImage($source, $destination, $quality = 85) {
    $info = getimagesize($source);
    
    if ($info === false) {
        return false;
    }
    
    switch ($info['mime']) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            imagejpeg($image, $destination, $quality);
            break;
            
        case 'image/png':
            $image = imagecreatefrompng($source);
            imagepng($image, $destination, 9 - round($quality / 10));
            break;
            
        case 'image/gif':
            $image = imagecreatefromgif($source);
            imagegif($image, $destination);
            break;
            
        case 'image/webp':
            $image = imagecreatefromwebp($source);
            imagewebp($image, $destination, $quality);
            break;
            
        default:
            return false;
    }
    
    imagedestroy($image);
    return true;
}

function createThumbnail($source, $destination, $maxWidth = 300, $maxHeight = 300) {
    $info = getimagesize($source);
    
    if ($info === false) {
        return false;
    }
    
    $width = $info[0];
    $height = $info[1];
    
    // Calculate new dimensions
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = round($width * $ratio);
    $newHeight = round($height * $ratio);
    
    // Create new image
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Load source image
    switch ($info['mime']) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($source);
            // Preserve transparency
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($source);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }
    
    // Resize
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, 
                      $newWidth, $newHeight, $width, $height);
    
    // Save thumbnail
    switch ($info['mime']) {
        case 'image/jpeg':
            imagejpeg($newImage, $destination, 85);
            break;
        case 'image/png':
            imagepng($newImage, $destination, 8);
            break;
        case 'image/gif':
            imagegif($newImage, $destination);
            break;
        case 'image/webp':
            imagewebp($newImage, $destination, 85);
            break;
    }
    
    imagedestroy($sourceImage);
    imagedestroy($newImage);
    
    return true;
}

// Process images
$images = glob($imageDir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
$processed = 0;
$saved = 0;

echo "Image Optimization Script\n";
echo "========================\n\n";

foreach ($images as $image) {
    $filename = basename($image);
    $optimizedPath = $optimizedDir . $filename;
    $thumbnailPath = $optimizedDir . 'thumb_' . $filename;
    
    // Skip if already optimized
    if (file_exists($optimizedPath)) {
        continue;
    }
    
    echo "Processing: $filename\n";
    
    $originalSize = filesize($image);
    
    // Optimize image
    if (optimizeImage($image, $optimizedPath)) {
        $optimizedSize = filesize($optimizedPath);
        $savedBytes = $originalSize - $optimizedSize;
        $savedPercent = round(($savedBytes / $originalSize) * 100, 2);
        
        echo "  - Optimized: " . formatBytes($originalSize) . " → " . formatBytes($optimizedSize);
        echo " (saved " . formatBytes($savedBytes) . ", $savedPercent%)\n";
        
        $saved += $savedBytes;
        
        // Create thumbnail
        if (createThumbnail($image, $thumbnailPath)) {
            $thumbSize = filesize($thumbnailPath);
            echo "  - Thumbnail created: " . formatBytes($thumbSize) . "\n";
        }
        
        $processed++;
    } else {
        echo "  - Failed to optimize\n";
    }
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

echo "\nSummary:\n";
echo "--------\n";
echo "Images processed: $processed\n";
echo "Total space saved: " . formatBytes($saved) . "\n";

// Create helper function for lazy loading
$helperFile = $rootDir . '/includes/image-helpers.php';
$helperContent = '<?php
function lazyImage($src, $alt = "", $class = "", $width = null, $height = null) {
    $optimizedDir = "/images/posts-images/optimized/";
    $filename = basename($src);
    $optimizedSrc = file_exists($_SERVER["DOCUMENT_ROOT"] . $optimizedDir . $filename) 
        ? $optimizedDir . $filename 
        : $src;
    
    $thumbnail = $optimizedDir . "thumb_" . $filename;
    $placeholderSrc = file_exists($_SERVER["DOCUMENT_ROOT"] . $thumbnail) 
        ? $thumbnail 
        : "data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1 1\'%3E%3C/svg%3E";
    
    $attrs = [];
    if ($width) $attrs[] = "width=\"$width\"";
    if ($height) $attrs[] = "height=\"$height\"";
    if ($class) $attrs[] = "class=\"$class\"";
    
    $attributes = implode(" ", $attrs);
    
    return "<img src=\"$placeholderSrc\" data-src=\"$optimizedSrc\" alt=\"" . htmlspecialchars($alt) . "\" $attributes loading=\"lazy\">";
}
';

file_put_contents($helperFile, $helperContent);
echo "\nHelper function created at: includes/image-helpers.php\n";