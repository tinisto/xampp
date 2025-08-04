<?php
// Image Upload Handler for TinyMCE
header('Content-Type: application/json');

// Security check - only allow admin users
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$response = [];

try {
    if (!isset($_FILES['file'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['file'];
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload error occurred');
    }

    // Check file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        throw new Exception('Invalid file type. Only images are allowed.');
    }

    // Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('File too large. Maximum size is 5MB.');
    }

    // Create upload directory if it doesn't exist
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/images/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_') . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to save file');
    }

    // Return success response with file URL
    $file_url = '/uploads/images/' . $filename;
    $response['location'] = $file_url;
    
} catch (Exception $e) {
    http_response_code(400);
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>