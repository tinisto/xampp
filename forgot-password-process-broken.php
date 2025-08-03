<?php
session_start();

// If accessed directly without POST, redirect to forgot password page
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /forgot-password');
    exit;
}

try {
    // Load required files with error checking
    $requiredFiles = [
        '/config/loadEnv.php',
        '/database/db_connections.php',
        '/includes/functions/email_functions.php',
        '/includes/email-templates/password-reset.php'
    ];
    
    foreach ($requiredFiles as $file) {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $file;
        if (!file_exists($filePath)) {
            error_log("Required file not found: " . $filePath);
            $_SESSION['reset_error'] = 'Сервис временно недоступен. Попробуйте позже.';
            header('Location: /forgot-password');
            exit;
        }
        require_once $filePath;
    }
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['reset_error'] = 'Недействительный запрос. Попробуйте еще раз.';
        header('Location: /forgot-password');
        exit;
    }
    
    // Handle form submission
    if (isset($_POST['email'])) {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Always show the same message for security
            $_SESSION['reset_success'] = 'Если указанный email зарегистрирован в системе, вы получите инструкции для восстановления пароля.';
            
            // Check if database connection exists
            if (!isset($connection) || !$connection) {
                error_log("Database connection not available");
                header('Location: /forgot-password');
                exit;
            }
            
            // Check if email exists and send reset email
            try {
                // First, create the password_resets table if it doesn't exist
                $createTableQuery = "CREATE TABLE IF NOT EXISTS password_resets (
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
                $connection->query($createTableQuery);
                
                // Check if email exists
                $stmt = $connection->prepare("SELECT id, firstname FROM users WHERE email = ?");
                if ($stmt) {
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $user = $result->fetch_assoc();
                        
                        // Generate secure token
                        $token = bin2hex(random_bytes(32));
                        $hashedToken = hash('sha256', $token);
                        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        
                        // Delete old tokens for this user
                        $deleteStmt = $connection->prepare("DELETE FROM password_resets WHERE user_id = ?");
                        if ($deleteStmt) {
                            $deleteStmt->bind_param("i", $user['id']);
                            $deleteStmt->execute();
                            $deleteStmt->close();
                        }
                        
                        // Store new token in database
                        $insertStmt = $connection->prepare("
                            INSERT INTO password_resets (user_id, token, expires_at) 
                            VALUES (?, ?, ?)
                        ");
                        
                        if ($insertStmt) {
                            $insertStmt->bind_param("iss", $user['id'], $hashedToken, $expiresAt);
                            if ($insertStmt->execute()) {
                                // Send email with reset link
                                $resetLink = "https://11klassniki.ru/reset-password?token=" . $token . "&email=" . urlencode($email);
                                $emailBody = getPasswordResetEmailTemplate($user['firstname'], $resetLink);
                                
                                // Send email using PHPMailer
                                sendPasswordResetEmail($email, $resetLink, 'Восстановление пароля - 11классники', $emailBody);
                                
                                // Log for debugging
                                error_log("Password reset email sent to: {$email}");
                            }
                            $insertStmt->close();
                        }
                    }
                    
                    $stmt->close();
                }
            } catch (Exception $e) {
                // Log error but don't expose it to user
                error_log("Password reset error: " . $e->getMessage());
            }
        } else {
            $_SESSION['reset_error'] = 'Пожалуйста, введите корректный email адрес.';
        }
    }
} catch (Exception $e) {
    error_log("Forgot password process error: " . $e->getMessage());
    $_SESSION['reset_error'] = 'Произошла ошибка. Попробуйте позже.';
}

// Always redirect back to forgot password page
header('Location: /forgot-password');
exit;
?>