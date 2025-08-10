<?php
// Image upload handler

class ImageUpload {
    private static $uploadDir = '/uploads/';
    private static $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private static $maxSize = 5242880; // 5MB
    
    public static function handleUpload($file, $type = 'avatar', $userId = null) {
        // Check if upload directory exists
        $fullUploadPath = $_SERVER['DOCUMENT_ROOT'] . self::$uploadDir . $type;
        if (!is_dir($fullUploadPath)) {
            mkdir($fullUploadPath, 0777, true);
        }
        
        // Validate file
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Ошибка загрузки файла'];
        }
        
        // Check file size
        if ($file['size'] > self::$maxSize) {
            return ['success' => false, 'error' => 'Файл слишком большой. Максимальный размер: 5MB'];
        }
        
        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, self::$allowedTypes)) {
            return ['success' => false, 'error' => 'Недопустимый тип файла. Разрешены: JPG, PNG, GIF, WebP'];
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid($type . '_' . ($userId ?: '') . '_') . '.' . $extension;
        $relativePath = self::$uploadDir . $type . '/' . $filename;
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $relativePath;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            // Resize image if it's too large
            self::resizeImage($fullPath, $mimeType, $type);
            
            return [
                'success' => true,
                'path' => $relativePath,
                'filename' => $filename,
                'url' => $relativePath
            ];
        }
        
        return ['success' => false, 'error' => 'Не удалось сохранить файл'];
    }
    
    private static function resizeImage($path, $mimeType, $type) {
        list($width, $height) = getimagesize($path);
        
        // Determine max dimensions based on type
        $maxWidth = $maxHeight = 1200;
        if ($type === 'avatar') {
            $maxWidth = $maxHeight = 400;
        } elseif ($type === 'thumbnail') {
            $maxWidth = $maxHeight = 300;
        }
        
        // Check if resize is needed
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return;
        }
        
        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);
        
        // Create image resource
        switch ($mimeType) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $source = imagecreatefrompng($path);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($path);
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($path);
                break;
            default:
                return;
        }
        
        // Create new image
        $destination = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Resize
        imagecopyresampled($destination, $source, 0, 0, 0, 0, 
                          $newWidth, $newHeight, $width, $height);
        
        // Save resized image
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($destination, $path, 85);
                break;
            case 'image/png':
                imagepng($destination, $path, 8);
                break;
            case 'image/gif':
                imagegif($destination, $path);
                break;
            case 'image/webp':
                imagewebp($destination, $path, 85);
                break;
        }
        
        // Clean up
        imagedestroy($source);
        imagedestroy($destination);
    }
    
    public static function deleteFile($path) {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $path;
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
    
    public static function getDefaultAvatar($name = '') {
        // Generate avatar with initials
        $initials = '';
        if ($name) {
            $parts = explode(' ', $name);
            foreach ($parts as $part) {
                if ($part) {
                    $initials .= mb_strtoupper(mb_substr($part, 0, 1));
                }
            }
        }
        $initials = $initials ?: 'U';
        
        // Generate color based on name
        $hash = md5($name);
        $color = '#' . substr($hash, 0, 6);
        
        // Return SVG data URL
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">';
        $svg .= '<rect width="100" height="100" fill="' . $color . '"/>';
        $svg .= '<text x="50" y="50" font-family="Arial" font-size="40" font-weight="bold" ';
        $svg .= 'text-anchor="middle" dominant-baseline="central" fill="white">' . $initials . '</text>';
        $svg .= '</svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}

// Helper function for profile avatars
function get_user_avatar($userId, $userName = '', $userAvatar = null) {
    if ($userAvatar && file_exists($_SERVER['DOCUMENT_ROOT'] . $userAvatar)) {
        return $userAvatar;
    }
    return ImageUpload::getDefaultAvatar($userName);
}
?>