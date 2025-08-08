<?php
// Delete Avatar Process
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Ensure user is logged in
requireLogin();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    // Get current user
    $userId = getCurrentUserId();
    if (!$userId) {
        throw new Exception('User not found');
    }

    // Get current avatar info
    $userQuery = "SELECT avatar FROM users WHERE id = ?";
    $stmt = mysqli_prepare($connection, $userQuery);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        throw new Exception('Пользователь не найден');
    }

    $currentAvatar = $user['avatar'];

    // Delete avatar file if it exists and is not default
    if (!empty($currentAvatar) && $currentAvatar !== 'default.png') {
        $avatarPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/avatars/' . $currentAvatar;
        if (file_exists($avatarPath)) {
            unlink($avatarPath);
        }

        // Also delete thumbnail if exists
        $thumbnailPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/avatars/thumbnails/' . $currentAvatar;
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
    }

    // Update user record to remove avatar
    $updateQuery = "UPDATE users SET avatar = NULL, updated_at = NOW() WHERE id = ?";
    $updateStmt = mysqli_prepare($connection, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "i", $userId);
    
    if (!mysqli_stmt_execute($updateStmt)) {
        throw new Exception('Ошибка при удалении аватара');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Аватар успешно удален'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>