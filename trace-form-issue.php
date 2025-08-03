<?php
if (!isset($_GET['secret']) || $_GET['secret'] !== 'debug123') {
    die('Access denied');
}

echo "<h2>Tracing Form Issue</h2>";
echo "<pre>";

$email = $_GET['email'] ?? '11klassniki.ru@gmail.com';

// Test 1: Check if the process file has the right field name
echo "=== Checking forgot-password-process.php ===\n";
$processFile = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/forgot-password-process.php');

if (strpos($processFile, 'firstname') !== false) {
    echo "❌ FOUND 'firstname' in process file (should be 'first_name')\n";
    $lines = explode("\n", $processFile);
    foreach ($lines as $num => $line) {
        if (strpos($line, 'firstname') !== false) {
            echo "  Line " . ($num + 1) . ": " . trim($line) . "\n";
        }
    }
} else {
    echo "✅ No 'firstname' found in process file\n";
}

if (strpos($processFile, 'first_name') !== false) {
    echo "✅ FOUND 'first_name' in process file\n";
} else {
    echo "❌ No 'first_name' found in process file\n";
}

// Test 2: Manually test the exact same email sending logic
echo "\n=== Testing Email Logic Manually ===\n";

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/password-reset.php';

if ($connection) {
    $stmt = $connection->prepare("SELECT id, first_name FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo "✅ User found: ID={$user['id']}, Name='{$user['first_name']}'\n";
            
            // Generate token like the process does
            $token = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $token);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete and insert token
            $deleteStmt = $connection->prepare("DELETE FROM password_resets WHERE user_id = ?");
            if ($deleteStmt) {
                $deleteStmt->bind_param("i", $user['id']);
                $deleteStmt->execute();
                $deleteStmt->close();
            }
            
            $insertStmt = $connection->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
            if ($insertStmt) {
                $insertStmt->bind_param("iss", $user['id'], $hashedToken, $expiresAt);
                if ($insertStmt->execute()) {
                    echo "✅ Token stored successfully\n";
                    
                    // Now send email exactly like the process file does
                    $resetLink = "https://11klassniki.ru/reset-password?token=" . $token . "&email=" . urlencode($email);
                    $emailBody = getPasswordResetEmailTemplate($user['first_name'], $resetLink);
                    
                    // Use PHPMailer directly like in the process file
                    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
                    
                    use PHPMailer\PHPMailer\PHPMailer;
                    use PHPMailer\PHPMailer\Exception;
                    
                    $mail = new PHPMailer(true);
                    
                    try {
                        // Server settings exactly like process file
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
                        $mail->Body    = $emailBody;
                        $mail->AltBody = strip_tags("Здравствуйте, {$user['first_name']}!\n\nДля восстановления пароля перейдите по ссылке:\n{$resetLink}\n\nСсылка действительна в течение 1 часа.");
                        
                        $mail->send();
                        echo "✅ Email sent successfully using exact process logic!\n";
                        
                    } catch (Exception $e) {
                        echo "❌ Email failed: {$mail->ErrorInfo}\n";
                    }
                    
                } else {
                    echo "❌ Failed to store token\n";
                }
                $insertStmt->close();
            }
        } else {
            echo "❌ No user found\n";
        }
        $stmt->close();
    }
}

echo "</pre>";
?>