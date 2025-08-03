<?php
// Debug login page
session_start();

// Generate CSRF token if needed
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Test database connection and user
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$debug_info = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $debug_info .= "<h3>Debug Login Attempt:</h3>";
    $debug_info .= "<p>Email: " . htmlspecialchars($email) . "</p>";
    $debug_info .= "<p>Password entered: " . str_repeat('*', strlen($password)) . " (" . strlen($password) . " chars)</p>";
    
    // Check if user exists
    $stmt = $connection->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $debug_info .= "<p style='color: green;'>✅ User found in database</p>";
        $debug_info .= "<p>User ID: " . $user['id'] . "</p>";
        $debug_info .= "<p>Is Active: " . (isset($user['is_active']) ? $user['is_active'] : 'N/A') . "</p>";
        
        // Test password
        $password_hash = $user['password'];
        $debug_info .= "<p>Password hash exists: " . (empty($password_hash) ? 'No' : 'Yes') . "</p>";
        $debug_info .= "<p>Hash length: " . strlen($password_hash) . "</p>";
        
        // Try password verification
        if (password_verify($password, $password_hash)) {
            $debug_info .= "<p style='color: green;'>✅ Password verified!</p>";
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            $debug_info .= "<p><a href='/account'>Go to Account</a></p>";
        } else {
            $debug_info .= "<p style='color: red;'>❌ Password verification failed</p>";
            
            // Try direct comparison for testing
            if ($password === 'password123') {
                $debug_info .= "<p style='color: orange;'>Test password matches, but hash doesn't verify</p>";
                $debug_info .= "<p>This means the password needs to be rehashed</p>";
            }
        }
    } else {
        $debug_info .= "<p style='color: red;'>❌ User not found</p>";
    }
    $stmt->close();
}

// Show test user info
$test_stmt = $connection->prepare("SELECT id, email, password, is_active FROM users WHERE email = 'test@example.com'");
$test_stmt->execute();
$test_result = $test_stmt->get_result();
if ($test_result->num_rows > 0) {
    $test_user = $test_result->fetch_assoc();
    $test_info = "<h3>Test User Status:</h3>";
    $test_info .= "<p>✅ Test user exists (ID: " . $test_user['id'] . ")</p>";
    $test_info .= "<p>Active: " . ($test_user['is_active'] ?? 'N/A') . "</p>";
    $test_info .= "<p>Has password: " . (!empty($test_user['password']) ? 'Yes' : 'No') . "</p>";
} else {
    $test_info = "<p style='color: red;'>❌ Test user not found</p>";
}
$test_stmt->close();

$connection->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Login - 11-классники</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #28a745;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .debug {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 14px;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Debug Login</h1>
        
        <div class="info">
            <?= $test_info ?>
        </div>
        
        <?php if ($debug_info): ?>
            <div class="debug">
                <?= $debug_info ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" value="test@example.com" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" value="password123" required>
            </div>
            
            <button type="submit">Debug Login</button>
        </form>
        
        <div style="margin-top: 30px;">
            <p><a href="/update_test_user.php">Update Test User Password</a> | 
               <a href="/login-simple.php">Simple Login</a> | 
               <a href="/">Homepage</a></p>
        </div>
    </div>
</body>
</html>