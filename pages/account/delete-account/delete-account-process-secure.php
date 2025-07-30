<?php
require_once __DIR__ . '/../../../includes/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check_user.php';

// Validate CSRF token
if (!Security::isValidCSRFToken()) {
    $_SESSION['error'] = 'Invalid security token. Please try again.';
    header('Location: /account/delete-account');
    exit();
}

// Get user data
$userId = $_SESSION['user_id'];
$userEmail = $_SESSION['email'];
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Prevent admin self-deletion
if ($isAdmin) {
    ErrorHandler::log('Admin attempted self-deletion', 'warning', [
        'admin_id' => $userId,
        'admin_email' => $userEmail
    ]);
    
    $_SESSION['error'] = 'Администраторы не могут удалить свой собственный аккаунт.';
    header('Location: /account');
    exit();
}

// Validate password
$password = $_POST['password'] ?? '';
$confirm = isset($_POST['confirm']) && $_POST['confirm'] === 'on';

if (!$confirm) {
    $_SESSION['error'] = 'Вы должны подтвердить удаление аккаунта.';
    header('Location: /account/delete-account');
    exit();
}

// Initialize database
$db = Database::getInstance($connection);

try {
    // Get user password hash
    $user = $db->queryOne("SELECT password FROM users WHERE id_users = ?", [$userId]);
    
    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Неверный пароль.';
        header('Location: /account/delete-account');
        exit();
    }
    
    // Log account deletion
    ErrorHandler::log('Account deletion initiated', 'info', [
        'user_id' => $userId,
        'user_email' => $userEmail
    ]);
    
    // Start transaction
    $db->beginTransaction();
    
    // Get avatar filename before deletion
    $avatarData = $db->queryOne("SELECT avatar FROM users WHERE id_users = ?", [$userId]);
    $avatarFilename = $avatarData['avatar'] ?? null;
    
    // Delete child comments first
    $db->execute("DELETE FROM child_comments WHERE id_user = ?", [$userId]);
    
    // Delete parent comments
    $db->execute("DELETE FROM comments WHERE user_id = ?", [$userId]);
    
    // Delete news/posts if user is an author
    $db->execute("DELETE FROM news WHERE author_id = ?", [$userId]);
    $db->execute("DELETE FROM posts WHERE author_id = ?", [$userId]);
    
    // Delete user account
    $db->execute("DELETE FROM users WHERE id_users = ?", [$userId]);
    
    // Commit transaction
    $db->commit();
    
    // Delete avatar file if exists
    if ($avatarFilename && $avatarFilename !== 'default.png') {
        $avatarPath = $_SERVER['DOCUMENT_ROOT'] . '/images/avatars/' . $avatarFilename;
        if (file_exists($avatarPath)) {
            unlink($avatarPath);
        }
    }
    
    // Log successful deletion
    ErrorHandler::log('Account deleted successfully', 'info', [
        'deleted_user_id' => $userId,
        'deleted_user_email' => $userEmail
    ]);
    
    // Destroy session
    session_destroy();
    
    // Redirect to success page
    header('Location: /account/delete-account/delete-success');
    exit();
    
} catch (Exception $e) {
    // Rollback on error
    $db->rollback();
    
    ErrorHandler::log('Account deletion failed: ' . $e->getMessage(), 'error', [
        'user_id' => $userId,
        'error' => $e->getMessage()
    ]);
    
    $_SESSION['error'] = 'Произошла ошибка при удалении аккаунта. Попробуйте позже.';
    header('Location: /account/delete-account');
    exit();
}