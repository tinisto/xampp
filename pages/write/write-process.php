<?php
/**
 * Process article submission from write-new.php
 */

// Start session if not already started
if (session_status() === PHP_SESSION_ID_NONE) {
    session_start();
}

// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/includes/db_connection.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /write");
    exit();
}

// Sanitize and validate form data
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';

// Validation errors array
$errors = [];

// Validate required fields
if (empty($title)) {
    $errors[] = "Заголовок статьи обязателен";
}

if (empty($category)) {
    $errors[] = "Выберите категорию";
}

if (empty($content)) {
    $errors[] = "Содержание статьи обязательно";
}

// If there are validation errors, redirect back with errors
if (!empty($errors)) {
    $_SESSION['write_errors'] = $errors;
    $_SESSION['write_data'] = [
        'title' => $title,
        'category' => $category,
        'content' => $content,
        'tags' => $tags
    ];
    header("Location: /write");
    exit();
}

// Get user ID from session (if user is logged in)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'anonymous@example.com';

// Prepare slug from title
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

// Insert article into database
try {
    $stmt = $connection->prepare("
        INSERT INTO posts (title, slug, content, category, tags, author_id, author_email, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    
    $stmt->bind_param("sssssss", $title, $slug, $content, $category, $tags, $user_id, $user_email);
    
    if ($stmt->execute()) {
        // Success - clear any session data
        unset($_SESSION['write_errors']);
        unset($_SESSION['write_data']);
        
        // Send notification email to admin
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php')) {
            include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
            
            $subject = 'Новая статья ожидает модерации';
            $body = "Новая статья была отправлена на модерацию.<br><br>";
            $body .= "<strong>Заголовок:</strong> " . htmlspecialchars($title) . "<br>";
            $body .= "<strong>Категория:</strong> " . htmlspecialchars($category) . "<br>";
            $body .= "<strong>Автор:</strong> " . htmlspecialchars($user_email) . "<br>";
            $body .= "<strong>Теги:</strong> " . htmlspecialchars($tags) . "<br><br>";
            $body .= "<strong>Содержание:</strong><br>" . nl2br(htmlspecialchars($content));
            
            // Send email if function exists
            if (function_exists('sendToAdmin')) {
                sendToAdmin($subject, $body);
            }
        }
        
        // Redirect to success page
        $_SESSION['write_success'] = "Ваша статья успешно отправлена на модерацию. После проверки она будет опубликована.";
        header("Location: /write-success");
        exit();
        
    } else {
        throw new Exception("Ошибка при сохранении статьи");
    }
    
} catch (Exception $e) {
    $_SESSION['write_errors'] = ["Произошла ошибка при сохранении статьи. Пожалуйста, попробуйте позже."];
    $_SESSION['write_data'] = [
        'title' => $title,
        'category' => $category,
        'content' => $content,
        'tags' => $tags
    ];
    header("Location: /write");
    exit();
}
?>