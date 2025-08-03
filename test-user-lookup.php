<?php
if (!isset($_GET['secret']) || $_GET['secret'] !== 'debug123') {
    die('Access denied');
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$email = $_GET['email'] ?? '11klassniki.ru@gmail.com';

echo "<h2>Final User Lookup Test: $email</h2>";
echo "<pre>";

if (!$connection) {
    die("❌ Database connection failed\n");
}

// Test the exact query used in password reset
echo "=== Testing Exact Password Reset Query ===\n";
$stmt = $connection->prepare("SELECT id, first_name FROM users WHERE email = ?");
if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Query executed successfully\n";
    echo "Number of rows found: " . $result->num_rows . "\n";
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "✅ USER FOUND:\n";
        echo "  ID: {$user['id']}\n";
        echo "  Name: '{$user['first_name']}'\n";
        
        // Test the password reset process step by step
        echo "\n=== Testing Password Reset Process ===\n";
        
        // Generate token
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        echo "✅ Token generated: " . substr($token, 0, 20) . "...\n";
        
        // Delete old tokens
        $deleteStmt = $connection->prepare("DELETE FROM password_resets WHERE user_id = ?");
        if ($deleteStmt) {
            $deleteStmt->bind_param("i", $user['id']);
            $deleteStmt->execute();
            echo "✅ Old tokens deleted (affected rows: " . $deleteStmt->affected_rows . ")\n";
            $deleteStmt->close();
        }
        
        // Insert new token
        $insertStmt = $connection->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        if ($insertStmt) {
            $insertStmt->bind_param("iss", $user['id'], $hashedToken, $expiresAt);
            if ($insertStmt->execute()) {
                echo "✅ Token stored in database\n";
                
                // Now test email sending
                echo "\n=== Testing Email Send ===\n";
                require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/password-reset.php';
                
                $resetLink = "https://11klassniki.ru/reset-password?token=" . $token . "&email=" . urlencode($email);
                $emailBody = getPasswordResetEmailTemplate($user['first_name'], $resetLink);
                
                echo "Reset link: $resetLink\n";
                echo "Email body generated successfully\n";
                
                try {
                    sendPasswordResetEmail($email, $resetLink, 'Test Password Reset - 11klassniki', $emailBody);
                    echo "✅ Email sending function called successfully\n";
                } catch (Exception $e) {
                    echo "❌ Email error: " . $e->getMessage() . "\n";
                }
                
            } else {
                echo "❌ Failed to store token: " . $insertStmt->error . "\n";
            }
            $insertStmt->close();
        }
        
    } else {
        echo "❌ No user found with that email\n";
        
        // Let's see what users DO exist
        echo "\n=== All Users ===\n";
        $allUsers = $connection->query("SELECT id, first_name, email FROM users ORDER BY id LIMIT 10");
        if ($allUsers && $allUsers->num_rows > 0) {
            while ($row = $allUsers->fetch_assoc()) {
                echo "  ID: {$row['id']}, Name: '{$row['first_name']}', Email: '{$row['email']}'\n";
            }
        }
    }
    
    $stmt->close();
} else {
    echo "❌ Failed to prepare statement: " . $connection->error . "\n";
}

echo "</pre>";
?>