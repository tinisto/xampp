<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/redirectToErrorPage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf-protection.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/input-validator.php';

// Check if comment filter functions exist, if not create basic functions
if (!function_exists('sanitizeComment')) {
    function sanitizeComment($text) {
        // Basic sanitization
        $text = trim($text);
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        return $text;
    }
}

if (!function_exists('filterBadWords')) {
    function filterBadWords($text) {
        // Basic implementation - you can enhance this
        return $text;
    }
}

// Include email functions if available
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php')) {
    include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comment"]) && isset($_POST["parent_id"]) && isset($_POST["entity_type"]) && isset($_POST["id_entity"])) {

    // Verify CSRF token
    verifyCSRFToken();

    // Check if user is logged in
    if (!isset($_SESSION['email'])) {
        header('Location: /login?redirect=' . urlencode($_SERVER['HTTP_REFERER'] ?? '/'));
        exit();
    }

    // Validate comment text
    $commentText = InputValidator::validateText($_POST['comment'] ?? '', 1, 2000);
    if (!$commentText) {
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/') . '?error=invalid_comment');
        exit();
    }
    
    // Additional filtering
    $commentText = filterBadWords($commentText);

    // Validate entity type
    $entityType = InputValidator::validateText($_POST['entity_type'] ?? '', 1, 20);
    
    // Validate IDs
    $id_entity = InputValidator::validateInt($_POST['id_entity'] ?? 0, 1, PHP_INT_MAX);
    $parent_id = InputValidator::validateInt($_POST['parent_id'] ?? 0, 0, PHP_INT_MAX);
    
    if (!$id_entity) {
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/') . '?error=invalid_id');
        exit();
    }

    // Validate entity type
    $validEntityTypes = ['school', 'vpo', 'spo', 'post'];
    if (!in_array($entityType, $validEntityTypes)) {
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/') . '?error=invalid_entity');
        exit();
    }

    // Validate entity ID
    if ($id_entity <= 0) {
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/') . '?error=invalid_id');
        exit();
    }

    try {
        // Get user ID from email
        $email = $_SESSION['email'];
        $queryUserId = "SELECT id FROM users WHERE email = ?";
        $stmtUserId = $connection->prepare($queryUserId);
        
        if (!$stmtUserId) {
            throw new Exception("Failed to prepare user query: " . $connection->error);
        }

        $stmtUserId->bind_param("s", $email);
        $stmtUserId->execute();
        $resultUserId = $stmtUserId->get_result();

        if ($resultUserId->num_rows === 0) {
            throw new Exception("User not found");
        }

        $rowUserId = $resultUserId->fetch_assoc();
        $user_id = $rowUserId['id'];
        $stmtUserId->close();

        // Insert comment
        $insertCommentQuery = "INSERT INTO comments (entity_id, user_id, comment_text, parent_id, entity_type, date) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmtInsertComment = $connection->prepare($insertCommentQuery);

        if (!$stmtInsertComment) {
            throw new Exception("Failed to prepare comment insert: " . $connection->error);
        }

        $stmtInsertComment->bind_param("iisis", $id_entity, $user_id, $commentText, $parent_id, $entityType);

        if (!$stmtInsertComment->execute()) {
            throw new Exception("Failed to insert comment: " . $stmtInsertComment->error);
        }

        $stmtInsertComment->close();

        // Send email notification to admin if function exists
        if (function_exists('sendToAdmin')) {
            $subject = 'New Comment Notification';
            $body = "A new comment has been posted.<br><br>Comment Text: " . nl2br(htmlspecialchars($commentText)) . "<br><br>Entity: " . $entityType . " (ID: " . $id_entity . ")";
            sendToAdmin($subject, $body);
        }

        // Redirect back to the original page with success message
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/';
        $separator = strpos($redirectUrl, '?') !== false ? '&' : '?';
        header('Location: ' . $redirectUrl . $separator . 'comment_success=1');
        exit();

    } catch (Exception $e) {
        // Log error and redirect with error message
        error_log("Comment submission error: " . $e->getMessage());
        
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/';
        $separator = strpos($redirectUrl, '?') !== false ? '&' : '?';
        header('Location: ' . $redirectUrl . $separator . 'error=comment_failed');
        exit();
    }

} else {
    // Invalid request - redirect to home
    header('Location: /');
    exit();
}
?>