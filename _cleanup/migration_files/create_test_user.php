<?php
// Create test user
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Create Test User</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// Check users table structure
echo "<h2>Users Table Structure:</h2>";
$cols = $connection->query("SHOW COLUMNS FROM users");
if ($cols) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th></tr>";
    while ($col = $cols->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='padding: 5px;'>" . $col['Field'] . "</td>";
        echo "<td style='padding: 5px;'>" . $col['Type'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check if test user exists
$check = $connection->prepare("SELECT * FROM users WHERE email = 'test@example.com'");
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "<p style='color: orange;'>⚠️ Test user already exists</p>";
    $user = $result->fetch_assoc();
    echo "<p>User ID: " . $user['id'] . "</p>";
    echo "<p>Email: " . $user['email'] . "</p>";
    
    // Update password to known value
    if (isset($_GET['update']) && $_GET['update'] == 'yes') {
        $new_password = password_hash('password123', PASSWORD_DEFAULT);
        $update = $connection->prepare("UPDATE users SET password = ? WHERE email = 'test@example.com'");
        $update->bind_param("s", $new_password);
        if ($update->execute()) {
            echo "<p style='color: green;'>✅ Password updated to: password123</p>";
        }
        $update->close();
    } else {
        echo "<p><a href='?update=yes' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none;'>Update Password to password123</a></p>";
    }
} else {
    // Create test user
    if (isset($_GET['create']) && $_GET['create'] == 'yes') {
        $email = 'test@example.com';
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $firstname = 'Test';
        $lastname = 'User';
        $occupation = 'Родитель';
        $role = 'user';
        $is_active = 1;
        
        // Check which columns exist
        $has_user_id = false;
        $has_is_active = false;
        $cols_check = $connection->query("SHOW COLUMNS FROM users");
        while ($col = $cols_check->fetch_assoc()) {
            if ($col['Field'] == 'user_id') $has_user_id = true;
            if ($col['Field'] == 'is_active') $has_is_active = true;
        }
        
        // Build insert query based on available columns
        if ($has_user_id) {
            $id_column = 'user_id';
        } else {
            $id_column = 'id';
        }
        
        $sql = "INSERT INTO users (email, password, first_name, last_name, occupation, role, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            echo "<p style='color: red;'>❌ Prepare failed: " . $connection->error . "</p>";
        } else {
            $stmt->bind_param("ssssssi", $email, $password, $firstname, $lastname, $occupation, $role, $is_active);
        }
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Test user created successfully!</p>";
            echo "<p>Email: test@example.com</p>";
            echo "<p>Password: password123</p>";
        } else {
            echo "<p style='color: red;'>❌ Error creating user: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p><a href='?create=yes' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none;'>Create Test User</a></p>";
    }
}

// Show existing users count
$count = $connection->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
echo "<p>Total users in database: $count</p>";

echo "<hr>";
echo "<p><a href='/login-simple.php'>Go to Simple Login</a> | <a href='/'>Homepage</a></p>";

$connection->close();
?>