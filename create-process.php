<?php
// Process content creation (news/posts)
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
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$content = $_POST['content'] ?? ''; // Rich HTML content from TinyMCE
$status = $_POST['status'] ?? 'published';
$news_type = $_POST['news_type'] ?? 'general';

// Validate required fields
if (empty($title) || empty($content)) {
    $_SESSION['error'] = 'Заголовок и содержание обязательны для заполнения';
    header('Location: /create/' . $content_type);
    exit;
}

// Handle image upload if provided
$image_path = null;
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
    // Check if news table exists and get column info
    $check_sql = "SHOW COLUMNS FROM news";
    $check_result = $connection->query($check_sql);
    
    if (!$check_result) {
        $_SESSION['error'] = 'Таблица news не найдена';
        header('Location: /create/' . $content_type);
        exit;
    }
    
    // Insert into news table with correct column names
    $approved = ($status === 'published') ? 1 : 0;
    $url_news = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $title));
    
    // Always try to save image if we have one
    if ($image_path) {
        $sql = "INSERT INTO news (title_news, text_news, description_news, author_news, category_news, url_news, image_news, date_news, approved, user_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $_SESSION['error'] = 'Ошибка подготовки запроса: ' . $connection->error;
            header('Location: /create/' . $content_type);
            exit;
        }
        $stmt->bind_param("sssssssii", $title, $content, $description, $author, $news_type, $url_news, $image_path, $approved, $user_id);
    } else {
        $sql = "INSERT INTO news (title_news, text_news, description_news, author_news, category_news, url_news, date_news, approved, user_id) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $_SESSION['error'] = 'Ошибка подготовки запроса: ' . $connection->error;
            header('Location: /create/' . $content_type);
            exit;
        }
        $stmt->bind_param("ssssssii", $title, $content, $description, $author, $news_type, $url_news, $approved, $user_id);
    }
    
} else {
    // Check if posts table exists
    $check_sql = "SHOW COLUMNS FROM posts";
    $check_result = $connection->query($check_sql);
    
    if (!$check_result) {
        $_SESSION['error'] = 'Таблица posts не найдена';
        header('Location: /create/' . $content_type);
        exit;
    }
    
    // Insert into posts table with correct column names
    $url_post = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $title));
    $category = 1; // Default category
    
    // Check if we have image_post column
    $check_image_col = "SHOW COLUMNS FROM posts LIKE 'image_post'";
    $has_image_col = $connection->query($check_image_col)->num_rows > 0;
    
    if ($has_image_col && $image_path) {
        $sql = "INSERT INTO posts (title_post, text_post, description_post, author_post, url_post, image_post, date_post, category, meta_d_post, meta_k_post, view_post) 
                VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?, '', '', 0)";
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $_SESSION['error'] = 'Ошибка подготовки запроса: ' . $connection->error;
            header('Location: /create/' . $content_type);
            exit;
        }
        $stmt->bind_param("ssssssi", $title, $content, $description, $author, $url_post, $image_path, $category);
    } else {
        $sql = "INSERT INTO posts (title_post, text_post, description_post, author_post, url_post, date_post, category, meta_d_post, meta_k_post, view_post) 
                VALUES (?, ?, ?, ?, ?, CURDATE(), ?, '', '', 0)";
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $_SESSION['error'] = 'Ошибка подготовки запроса: ' . $connection->error;
            header('Location: /create/' . $content_type);
            exit;
        }
        $stmt->bind_param("sssssi", $title, $content, $description, $author, $url_post, $category);
    }
}

// Execute query
if ($stmt->execute()) {
    $insert_id = $connection->insert_id;
    $success_message = $content_type === 'news' ? 'Новость успешно создана!' : 'Пост успешно создан!';
    $_SESSION['success'] = $success_message;
    
    // Redirect to the created item
    if ($content_type === 'news') {
        // For news, redirect to the news page
        header('Location: /news/' . $url_news);
    } else {
        // For posts, redirect to the post page
        header('Location: /post/' . $url_post);
    }
} else {
    $_SESSION['error'] = 'Ошибка при сохранении: ' . $connection->error;
    header('Location: /create/' . $content_type);
}

$stmt->close();
$connection->close();
?>