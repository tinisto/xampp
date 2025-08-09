<?php
/**
 * Image Upload API for Comments
 * Handles image uploads with security and optimization
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit();
}

// Configuration
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/comments/';
$uploadUrl = '/uploads/comments/';
$maxFileSize = 5 * 1024 * 1024; // 5MB
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$maxWidth = 1920;
$maxHeight = 1080;

// Create upload directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
    
    // Create .htaccess to prevent PHP execution
    file_put_contents($uploadDir . '.htaccess', "Options -Indexes\n<FilesMatch '\.(php|phtml|php3|php4|php5|pl|py|jsp|asp|sh|cgi)$'>\n    deny from all\n</FilesMatch>");
}

// Check if it's a POST request with file
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['image'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    exit();
}

$file = $_FILES['image'];

// Validate file upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];
    
    $error = $errorMessages[$file['error']] ?? 'Unknown upload error';
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $error]);
    exit();
}

// Validate file size
if ($file['size'] > $maxFileSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'File too large. Maximum size is 5MB']);
    exit();
}

// Validate MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPEG, PNG, GIF and WebP are allowed']);
    exit();
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('comment_') . '_' . time() . '.' . strtolower($extension);
$uploadPath = $uploadDir . $filename;

try {
    // Load and process image
    $image = null;
    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            $image = imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/png':
            $image = imagecreatefrompng($file['tmp_name']);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($file['tmp_name']);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($file['tmp_name']);
            break;
    }
    
    if (!$image) {
        throw new Exception('Failed to process image');
    }
    
    // Get original dimensions
    $origWidth = imagesx($image);
    $origHeight = imagesy($image);
    
    // Calculate new dimensions if needed
    if ($origWidth > $maxWidth || $origHeight > $maxHeight) {
        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
        $newWidth = round($origWidth * $ratio);
        $newHeight = round($origHeight * $ratio);
        
        // Create resized image
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagecolortransparent($resized, imagecolorallocatealpha($resized, 0, 0, 0, 127));
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }
        
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagedestroy($image);
        $image = $resized;
    }
    
    // Save optimized image
    $saved = false;
    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            $saved = imagejpeg($image, $uploadPath, 85); // 85% quality
            break;
        case 'image/png':
            $saved = imagepng($image, $uploadPath, 6); // Compression level 6
            break;
        case 'image/gif':
            $saved = imagegif($image, $uploadPath);
            break;
        case 'image/webp':
            $saved = imagewebp($image, $uploadPath, 85);
            break;
    }
    
    imagedestroy($image);
    
    if (!$saved) {
        throw new Exception('Failed to save image');
    }
    
    // Generate thumbnail
    $thumbFilename = 'thumb_' . $filename;
    $thumbPath = $uploadDir . $thumbFilename;
    $thumbWidth = 200;
    $thumbHeight = 200;
    
    // Create thumbnail
    $original = null;
    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            $original = imagecreatefromjpeg($uploadPath);
            break;
        case 'image/png':
            $original = imagecreatefrompng($uploadPath);
            break;
        case 'image/gif':
            $original = imagecreatefromgif($uploadPath);
            break;
        case 'image/webp':
            $original = imagecreatefromwebp($uploadPath);
            break;
    }
    
    if ($original) {
        $origWidth = imagesx($original);
        $origHeight = imagesy($original);
        
        // Calculate thumbnail dimensions (center crop)
        $ratio = max($thumbWidth / $origWidth, $thumbHeight / $origHeight);
        $newWidth = round($origWidth * $ratio);
        $newHeight = round($origHeight * $ratio);
        
        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        
        // Preserve transparency
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }
        
        // Center crop
        $srcX = ($newWidth - $thumbWidth) / 2;
        $srcY = ($newHeight - $thumbHeight) / 2;
        
        imagecopyresampled($thumb, $original, 0, 0, $srcX, $srcY, $thumbWidth, $thumbHeight, $origWidth, $origHeight);
        
        // Save thumbnail
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                imagejpeg($thumb, $thumbPath, 85);
                break;
            case 'image/png':
                imagepng($thumb, $thumbPath, 6);
                break;
            case 'image/gif':
                imagegif($thumb, $thumbPath);
                break;
            case 'image/webp':
                imagewebp($thumb, $thumbPath, 85);
                break;
        }
        
        imagedestroy($original);
        imagedestroy($thumb);
    }
    
    // Log upload to database (optional)
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    $userId = $_SESSION['user_id'] ?? null;
    $userEmail = $_SESSION['email'] ?? '';
    $clientIP = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    $logQuery = "INSERT INTO comment_uploads (user_id, user_email, filename, original_name, file_size, mime_type, ip_address) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $connection->prepare($logQuery);
    if ($stmt) {
        $stmt->bind_param("isssiss", $userId, $userEmail, $filename, $file['name'], $file['size'], $mimeType, $clientIP);
        $stmt->execute();
        $stmt->close();
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'url' => $uploadUrl . $filename,
        'thumbnail' => $uploadUrl . $thumbFilename,
        'filename' => $filename,
        'size' => filesize($uploadPath)
    ]);
    
} catch (Exception $e) {
    // Clean up on error
    if (file_exists($uploadPath)) {
        unlink($uploadPath);
    }
    if (isset($thumbPath) && file_exists($thumbPath)) {
        unlink($thumbPath);
    }
    
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to process image: ' . $e->getMessage()]);
}
?>