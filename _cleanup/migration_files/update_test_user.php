<?php
// Update test user password
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Update Test User Password</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// Check if test user exists
$stmt = $connection->prepare("SELECT * FROM users WHERE email = 'test@example.com'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<h2>Test User Found:</h2>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><td style='padding: 5px;'><strong>ID:</strong></td><td style='padding: 5px;'>" . $user['id'] . "</td></tr>";
    echo "<tr><td style='padding: 5px;'><strong>Email:</strong></td><td style='padding: 5px;'>" . $user['email'] . "</td></tr>";
    echo "<tr><td style='padding: 5px;'><strong>Role:</strong></td><td style='padding: 5px;'>" . ($user['role'] ?? 'N/A') . "</td></tr>";
    echo "<tr><td style='padding: 5px;'><strong>Is Active:</strong></td><td style='padding: 5px;'>" . (isset($user['is_active']) ? ($user['is_active'] ? 'Yes' : 'No') : 'N/A') . "</td></tr>";
    echo "</table>";
    
    if (isset($_GET['update']) && $_GET['update'] == 'yes') {
        // Update password
        $new_password = password_hash('password123', PASSWORD_DEFAULT);
        
        $update_sql = "UPDATE users SET password = ?";
        $params = [$new_password];
        $types = "s";
        
        // Check if is_active column exists and set it to 1
        $cols = $connection->query("SHOW COLUMNS FROM users LIKE 'is_active'");
        if ($cols->num_rows > 0) {
            $update_sql .= ", is_active = ?";
            $params[] = 1;
            $types .= "i";
        }
        
        $update_sql .= " WHERE email = ?";
        $params[] = 'test@example.com';
        $types .= "s";
        
        $update_stmt = $connection->prepare($update_sql);
        $update_stmt->bind_param($types, ...$params);
        
        if ($update_stmt->execute()) {
            echo "<p style='color: green; font-size: 18px;'>✅ Password updated successfully!</p>";
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 20px 0;'>";
            echo "<h3>Test Account Credentials:</h3>";
            echo "<p><strong>Email:</strong> test@example.com</p>";
            echo "<p><strong>Password:</strong> password123</p>";
            echo "</div>";
            echo "<p><a href='/login-simple.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none;'>Go to Login</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Error updating password: " . $update_stmt->error . "</p>";
        }
        $update_stmt->close();
    } else {
        echo "<p style='margin-top: 20px;'><a href='?update=yes' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none;'>Update Password to: password123</a></p>";
    }
} else {
    echo "<p style='color: red;'>❌ Test user not found!</p>";
    echo "<p>Would you like to create a different test user?</p>";
    
    if (isset($_GET['create_new']) && $_GET['create_new'] == 'yes') {
        $email = 'test2@example.com';
        $password = password_hash('password123', PASSWORD_DEFAULT);
        
        // Get column names
        $columns = [];
        $cols_check = $connection->query("SHOW COLUMNS FROM users");
        while ($col = $cols_check->fetch_assoc()) {
            $columns[] = $col['Field'];
        }
        
        // Build minimal insert
        $insert_sql = "INSERT INTO users (email, password";
        $values = "('$email', '$password'";
        
        if (in_array('role', $columns)) {
            $insert_sql .= ", role";
            $values .= ", 'user'";
        }
        if (in_array('is_active', $columns)) {
            $insert_sql .= ", is_active";
            $values .= ", 1";
        }
        
        $insert_sql .= ") VALUES " . $values . ")";
        
        if ($connection->query($insert_sql)) {
            echo "<p style='color: green;'>✅ New test user created!</p>";
            echo "<p>Email: test2@example.com<br>Password: password123</p>";
        } else {
            echo "<p style='color: red;'>❌ Error: " . $connection->error . "</p>";
        }
    } else {
        echo "<p><a href='?create_new=yes' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none;'>Create test2@example.com</a></p>";
    }
}

$stmt->close();

echo "<hr>";
echo "<p><a href='/login-simple.php'>Simple Login</a> | <a href='/check_users_table.php'>Check Users Table</a> | <a href='/'>Homepage</a></p>";

$connection->close();
?>