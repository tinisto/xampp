<?php
/**
 * Update User Profile API
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get POST data
$firstName = trim($_POST['first_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$occupation = $_POST['occupation'] ?? '';

// Validate input
if (empty($firstName)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Имя обязательно для заполнения']);
    exit();
}

if (strlen($firstName) < 2 || strlen($firstName) > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Имя должно быть от 2 до 100 символов']);
    exit();
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Некорректный email адрес']);
    exit();
}

// Check if email is already taken by another user
$emailCheckQuery = "SELECT id FROM users WHERE email = ? AND id != ?";
$stmt = $connection->prepare($emailCheckQuery);
$stmt->bind_param("si", $email, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email уже используется другим пользователем']);
    exit();
}

// Update user profile
$updateQuery = "UPDATE users SET first_name = ?, email = ?, occupation = ? WHERE id = ?";
$stmt = $connection->prepare($updateQuery);
$stmt->bind_param("sssi", $firstName, $email, $occupation, $_SESSION['user_id']);

if ($stmt->execute()) {
    // Update session data
    $_SESSION['first_name'] = $firstName;
    $_SESSION['email'] = $email;
    $_SESSION['occupation'] = $occupation;
    
    echo json_encode(['success' => true, 'message' => 'Профиль успешно обновлен']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении профиля']);
}
?>