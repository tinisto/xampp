<?php
session_start();

// Simple CSRF validation
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Ошибка безопасности. Попробуйте снова.';
    header('Location: /registration');
    exit();
}

// Get form data
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$occupation = $_POST['occupation'] ?? '';
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// Validate inputs
$errors = [];

if (empty($firstname)) {
    $errors[] = 'Введите имя';
}

if (empty($lastname)) {
    $errors[] = 'Введите фамилию';
}

if (empty($occupation)) {
    $errors[] = 'Выберите род деятельности';
}

if (!$email) {
    $errors[] = 'Введите корректный email';
}

if (strlen($password) < 8) {
    $errors[] = 'Пароль должен содержать минимум 8 символов';
}

if ($password !== $confirmPassword) {
    $errors[] = 'Пароли не совпадают';
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['oldData'] = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'occupation' => $occupation,
        'email' => $email
    ];
    header('Location: /registration');
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASS') || !defined('DB_NAME')) {
    $_SESSION['error'] = 'Ошибка конфигурации базы данных.';
    header('Location: /registration');
    exit();
}

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connection->connect_error) {
    $_SESSION['error'] = 'Ошибка подключения к базе данных.';
    header('Location: /registration');
    exit();
}

$connection->set_charset("utf8mb4");

// Check if email already exists
$stmt = $connection->prepare("SELECT id FROM users WHERE email = ?");
if (!$stmt) {
    $_SESSION['error'] = 'Ошибка базы данных: ' . $connection->error;
    header('Location: /registration');
    exit();
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error'] = 'Пользователь с таким email уже существует.';
    header('Location: /registration');
    exit();
}

// Avatar handling will be added later when updating profile

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Generate activation token
$activationToken = bin2hex(random_bytes(32));
$role = 'user';
$timezone = $_POST['timezone'] ?? 'UTC';

// Insert new user (based on original table structure)
$stmt = $connection->prepare("INSERT INTO users (password, email, role, activation_token, timezone, occupation) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    $_SESSION['error'] = 'Ошибка базы данных: ' . $connection->error;
    header('Location: /registration');
    exit();
}
$stmt->bind_param("ssssss", $hashedPassword, $email, $role, $activationToken, $timezone, $occupation);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Регистрация успешна! Теперь вы можете войти.';
    header('Location: /login');
} else {
    $_SESSION['error'] = 'Ошибка при регистрации. Попробуйте снова.';
    header('Location: /registration');
}

$stmt->close();
$connection->close();
exit();