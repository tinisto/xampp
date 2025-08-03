<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Debug output
echo "<h3>Debug Information:</h3>";
echo "<pre>";
echo "Session started: " . (session_id() ? 'Yes' : 'No') . "\n";
echo "Request method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "POST data: " . print_r($_POST, true) . "\n";

// Try to load files one by one
echo "\nTrying to load files:\n";

// 1. LoadEnv
$loadEnvPath = $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
echo "1. loadEnv.php path: $loadEnvPath\n";
echo "   File exists: " . (file_exists($loadEnvPath) ? 'Yes' : 'No') . "\n";
if (file_exists($loadEnvPath)) {
    require_once $loadEnvPath;
    echo "   Loaded successfully\n";
}

// 2. Database connections
$dbPath = $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
echo "\n2. db_connections.php path: $dbPath\n";
echo "   File exists: " . (file_exists($dbPath) ? 'Yes' : 'No') . "\n";
if (file_exists($dbPath)) {
    require_once $dbPath;
    echo "   Loaded successfully\n";
    echo "   Database connection: " . (isset($connection) ? 'Established' : 'Failed') . "\n";
}

// Check if we can proceed
if (!isset($connection)) {
    echo "\nERROR: Database connection not established!\n";
    echo "</pre>";
    exit;
}

echo "\nAll files loaded successfully!\n";
echo "</pre>";

// Simple password reset logic without dependencies
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<p>This page only accepts POST requests.</p>";
    exit;
}

// CSRF validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['reset_error'] = 'Недействительный токен безопасности';
    header('Location: /forgot-password');
    exit;
}

// Validate email
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['reset_error'] = 'Неверный формат email';
    header('Location: /forgot-password');
    exit;
}

try {
    // Check if user exists
    $stmt = $connection->prepare("SELECT id, firstname, email FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $connection->error);
    }
    
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Don't reveal if email exists or not for security
        $_SESSION['reset_success'] = 'Если этот email зарегистрирован, вы получите инструкции по восстановлению пароля.';
        header('Location: /forgot-password');
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Generate reset token
    $token = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $token);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Create password_resets table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(255) NOT NULL,
        expires_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        used BOOLEAN DEFAULT FALSE,
        INDEX idx_user_id (user_id),
        INDEX idx_token (token),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    if (!$connection->query($createTable)) {
        throw new Exception("Create table failed: " . $connection->error);
    }
    
    // Save token to database
    $stmt = $connection->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare insert failed: " . $connection->error);
    }
    
    $stmt->bind_param("iss", $user['id'], $hashedToken, $expiresAt);
    if (!$stmt->execute()) {
        throw new Exception("Insert failed: " . $stmt->error);
    }
    
    // Generate reset link
    $resetLink = "https://11klassniki.ru/reset-password?token=" . $token . "&email=" . urlencode($email);
    
    // For now, just show the link since email might not be configured
    $_SESSION['reset_link'] = $resetLink;
    $_SESSION['reset_success'] = 'Используйте эту ссылку для сброса пароля:';
    
} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    $_SESSION['reset_error'] = 'Произошла ошибка: ' . $e->getMessage();
}

header('Location: /forgot-password');
exit;