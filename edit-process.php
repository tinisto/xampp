<?php
// Process content editing (news/posts)
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check_admin.php';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /dashboard');
    exit;
}

// Get form data
$content_type = $_POST['content_type'] ?? 'news';
$item_id = $_POST['item_id'] ?? 0;
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$content = $_POST['content'] ?? ''; // Rich HTML content from TinyMCE
$status = $_POST['status'] ?? 'published';
$news_type = $_POST['news_type'] ?? 'general';
$current_image = $_POST['current_image'] ?? '';

// Validate required fields
if (empty($title) || empty($content) || !$item_id) {
    $_SESSION['error'] = 'Заголовок и содержание обязательны для заполнения';
    header('Location: /edit/' . $content_type . '/' . $item_id);
    exit;
}

// Handle image upload if provided
$image_path = $current_image; // Keep current image by default
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/content/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Validate image
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);
    
    if (in_array($mime_type, $allowed_types)) {
        // Generate unique filename
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid($content_type . '_') . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $image_path = '/uploads/content/' . $filename;
        }
    }
}

// Get user info
$user_id = $_SESSION['user_id'] ?? 0;
$author = $_SESSION['username'] ?? $_SESSION['email'] ?? 'Admin';

// Prepare data based on content type
if ($content_type === 'news') {
    // Update news
    $approved = ($status === 'published') ? 1 : 0;
    $url_news = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $title));
    
    // Check if we have image_news column
    $check_image_col = "SHOW COLUMNS FROM news LIKE 'image_news'";
    $has_image_col = $connection->query($check_image_col)->num_rows > 0;
    
    if ($has_image_col) {
        $sql = "UPDATE news SET 
                title_news = ?, 
                text_news = ?, 
                description_news = ?, 
                category_news = ?, 
                url_slug = ?, 
                image_news = ?,
                approved = ?
                WHERE id_news = ?";
        
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $_SESSION['error'] = 'Ошибка подготовки запроса: ' . $connection->error;
            header('Location: /edit/' . $content_type . '/' . $item_id);
            exit;
        }
        
        $stmt->bind_param("ssssssii", $title, $content, $description, $news_type, $url_news, $image_path, $approved, $item_id);
    } else {
        $sql = "UPDATE news SET 
                title_news = ?, 
                text_news = ?, 
                description_news = ?, 
                category_news = ?, 
                url_slug = ?, 
                approved = ?
                WHERE id_news = ?";
        
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $_SESSION['error'] = 'Ошибка подготовки запроса: ' . $connection->error;
            header('Location: /edit/' . $content_type . '/' . $item_id);
            exit;
        }
        
        $stmt->bind_param("sssssii", $title, $content, $description, $news_type, $url_news, $approved, $item_id);
    }
    
} else {
    // Update posts
    $url_post = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $title));
    
    // Check if we have image_post column
    $check_image_col = "SHOW COLUMNS FROM posts LIKE 'image_post'";
    $has_image_col = $connection->query($check_image_col)->num_rows > 0;
    
    if ($has_image_col) {
        $sql = "UPDATE posts SET 
                title_post = ?, 
                text_post = ?, 
                description_post = ?, 
                url_slug = ?,
                image_post = ?
                WHERE id_post = ?";
        
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $_SESSION['error'] = 'Ошибка подготовки запроса: ' . $connection->error;
            header('Location: /edit/' . $content_type . '/' . $item_id);
            exit;
        }
        
        $stmt->bind_param("sssssi", $title, $content, $description, $url_post, $image_path, $item_id);
    } else {
        $sql = "UPDATE posts SET 
                title_post = ?, 
                text_post = ?, 
                description_post = ?, 
                url_slug = ?
                WHERE id_post = ?";
        
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $_SESSION['error'] = 'Ошибка подготовки запроса: ' . $connection->error;
            header('Location: /edit/' . $content_type . '/' . $item_id);
            exit;
        }
        
        $stmt->bind_param("ssssi", $title, $content, $description, $url_post, $item_id);
    }
}

// Execute query
if ($stmt->execute()) {
    $success_message = $content_type === 'news' ? 'Новость успешно обновлена!' : 'Пост успешно обновлен!';
    $_SESSION['success'] = $success_message;
    
    // Redirect to the updated item
    if ($content_type === 'news') {
        header('Location: /news/' . $url_news);
    } else {
        header('Location: /post/' . $url_post);
    }
} else {
    $_SESSION['error'] = 'Ошибка при сохранении: ' . $connection->error;
    header('Location: /edit/' . $content_type . '/' . $item_id);
}

$stmt->close();
$connection->close();
?>