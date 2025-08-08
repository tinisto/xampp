<?php
// Password Change Process
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

    // Get form data
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate input
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        throw new Exception('Все поля обязательны для заполнения');
    }

    if ($newPassword !== $confirmPassword) {
        throw new Exception('Новый пароль и его подтверждение не совпадают');
    }

    if (strlen($newPassword) < 6) {
        throw new Exception('Новый пароль должен содержать минимум 6 символов');
    }

    // Get current user data
    $userQuery = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($connection, $userQuery);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        throw new Exception('Пользователь не найден');
    }

    // Verify current password
    if (!password_verify($currentPassword, $user['password'])) {
        throw new Exception('Текущий пароль указан неверно');
    }

    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password
    $updateQuery = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
    $updateStmt = mysqli_prepare($connection, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "si", $hashedPassword, $userId);
    
    if (!mysqli_stmt_execute($updateStmt)) {
        throw new Exception('Ошибка при обновлении пароля');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Пароль успешно изменен'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>