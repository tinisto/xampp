<?php
session_start();

// Use statements must be at the top
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// If accessed directly without POST, redirect to forgot password page
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /forgot-password');
    exit;
}

try {
    // Load environment first
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    
    // Load database connection
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    // Load PHPMailer
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    // Load email template
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/password-reset.php';
    
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
                $stmt = $connection->prepare("SELECT id, first_name FROM users WHERE email = ?");
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
                                
                                // Send email using PHPMailer
                                $mail = new PHPMailer(true);
                                
                                try {
                                    // Server settings
                                    $mail->isSMTP();
                                    $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.ipage.com';
                                    $mail->SMTPAuth   = true;
                                    $mail->Username   = $_ENV['SMTP_USERNAME'] ?? '';
                                    $mail->Password   = $_ENV['SMTP_PASSWORD'] ?? '';
                                    $mail->SMTPSecure = $_ENV['SMTP_SECURITY'] ?? PHPMailer::ENCRYPTION_STARTTLS;
                                    $mail->Port       = intval($_ENV['SMTP_PORT'] ?? 587);
                                    $mail->CharSet    = 'UTF-8';
                                    
                                    // Recipients
                                    $fromEmail = $_ENV['ADMIN_EMAIL'] ?? 'noreply@11klassniki.ru';
                                    $mail->setFrom($fromEmail, '11классники');
                                    $mail->addAddress($email);
                                    
                                    // Content
                                    $mail->isHTML(true);
                                    $mail->Subject = 'Восстановление пароля - 11классники';
                                    $mail->Body    = getPasswordResetEmailTemplate($user['first_name'], $resetLink);
                                    $mail->AltBody = strip_tags("Здравствуйте, {$user['first_name']}!\n\nДля восстановления пароля перейдите по ссылке:\n{$resetLink}\n\nСсылка действительна в течение 1 часа.");
                                    
                                    $mail->send();
                                    error_log("Password reset email sent successfully to: {$email}");
                                } catch (Exception $e) {
                                    error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
                                }
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