<?php
/**
 * Emergency Account Activation
 * Single file solution - upload this ONE file to activate accounts
 */

// Direct database connection (since we know these work from your site)
$host = 'localhost';
$databases = ['11klassniki_claude', '11klone_livezilla'];
$users = ['11klone', '11klassniki'];
$password = 'K8HqqBV3hTf4mha';

$connection = null;
$error = '';

// Try to connect with different combinations
foreach ($databases as $db) {
    foreach ($users as $user) {
        try {
            $connection = new mysqli($host, $user, $password, $db);
            if (!$connection->connect_error) {
                break 2; // Connected successfully
            }
        } catch (Exception $e) {
            continue;
        }
    }
}

if (!$connection || $connection->connect_error) {
    $error = 'Database connection failed';
}

$message = '';
$messageType = '';

// Handle activation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && !$error) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Try to find and activate user
        $stmt = $connection->prepare("UPDATE users SET is_active = 1 WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $message = "✅ Account activated successfully! You can now login.";
                    $messageType = 'success';
                } else {
                    // Check if user exists
                    $check = $connection->prepare("SELECT is_active FROM users WHERE email = ?");
                    $check->bind_param("s", $email);
                    $check->execute();
                    $result = $check->get_result();
                    
                    if ($result->num_rows > 0) {
                        $user = $result->fetch_assoc();
                        if ($user['is_active'] == 1) {
                            $message = "ℹ️ This account is already activated.";
                            $messageType = 'info';
                        }
                    } else {
                        $message = "❌ No account found with this email address.";
                        $messageType = 'danger';
                    }
                }
            } else {
                $message = "❌ Activation failed. Please try again.";
                $messageType = 'danger';
            }
            $stmt->close();
        }
    } else {
        $message = "❌ Invalid email format.";
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Account Activation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 24px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="email"]:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .security-note {
            margin-top: 30px;
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            color: #856404;
            font-size: 14px;
        }
        
        .error-box {
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔓 Emergency Account Activation</h1>
        <p class="subtitle">Activate your account to fix login issues</p>
        
        <?php if ($error): ?>
            <div class="error-box">
                <strong>System Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$error): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        placeholder="your-email@example.com"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">Activate Account</button>
                <a href="/login" class="btn btn-secondary">Back to Login</a>
            </form>
            
            <div class="security-note">
                <strong>⚠️ Security Notice:</strong> This is a temporary activation tool. 
                Please delete this file after use or contact the administrator.
            </div>
        <?php else: ?>
            <p>Please contact the administrator to resolve this issue.</p>
            <a href="/login" class="btn btn-secondary">Back to Login</a>
        <?php endif; ?>
    </div>
</body>
</html>