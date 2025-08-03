<?php
// Check exact users table structure
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Users Table Structure Check</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// Show exact columns
echo "<h2>Exact Column Names:</h2>";
$cols = $connection->query("SHOW COLUMNS FROM users");
if ($cols) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Key</th><th>Default</th></tr>";
    while ($col = $cols->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='padding: 5px;'><strong>" . $col['Field'] . "</strong></td>";
        echo "<td style='padding: 5px;'>" . $col['Type'] . "</td>";
        echo "<td style='padding: 5px;'>" . $col['Key'] . "</td>";
        echo "<td style='padding: 5px;'>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test query
echo "<h2>Test Query:</h2>";
$test_email = 'test@example.com';
$query = "SELECT * FROM users WHERE email = ? LIMIT 1";
$stmt = $connection->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $test_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Found test user</p>";
        $user = $result->fetch_assoc();
        echo "<table border='1' style='border-collapse: collapse;'>";
        foreach ($user as $key => $value) {
            echo "<tr>";
            echo "<td style='padding: 5px;'><strong>$key</strong></td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars(substr($value ?? '', 0, 50)) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>No test user found</p>";
    }
    $stmt->close();
}

// Create corrected test user
if (isset($_GET['create']) && $_GET['create'] == 'yes') {
    echo "<h2>Creating Test User:</h2>";
    
    // First check exact column names
    $columns = [];
    $cols_check = $connection->query("SHOW COLUMNS FROM users");
    while ($col = $cols_check->fetch_assoc()) {
        $columns[] = $col['Field'];
    }
    
    echo "<p>Available columns: " . implode(', ', $columns) . "</p>";
    
    // Build insert based on actual columns
    $email = 'test@example.com';
    $password = password_hash('password123', PASSWORD_DEFAULT);
    
    // Check which name columns exist
    $has_firstname = in_array('firstname', $columns);
    $has_first_name = in_array('first_name', $columns);
    $has_name = in_array('name', $columns);
    
    if ($has_firstname) {
        $name_columns = "firstname, lastname";
        $name_values = "'Test', 'User'";
    } elseif ($has_first_name) {
        $name_columns = "first_name, last_name";
        $name_values = "'Test', 'User'";
    } elseif ($has_name) {
        $name_columns = "name";
        $name_values = "'Test User'";
    } else {
        $name_columns = "";
        $name_values = "";
    }
    
    // Build query
    $sql = "INSERT INTO users (email, password";
    if ($name_columns) {
        $sql .= ", " . $name_columns;
    }
    if (in_array('occupation', $columns)) {
        $sql .= ", occupation";
    }
    if (in_array('role', $columns)) {
        $sql .= ", role";
    }
    if (in_array('is_active', $columns)) {
        $sql .= ", is_active";
    }
    
    $sql .= ") VALUES ('$email', '$password'";
    if ($name_values) {
        $sql .= ", " . $name_values;
    }
    if (in_array('occupation', $columns)) {
        $sql .= ", 'Родитель'";
    }
    if (in_array('role', $columns)) {
        $sql .= ", 'user'";
    }
    if (in_array('is_active', $columns)) {
        $sql .= ", 1";
    }
    $sql .= ")";
    
    echo "<p>SQL: <code>" . htmlspecialchars($sql) . "</code></p>";
    
    if ($connection->query($sql)) {
        echo "<p style='color: green;'>✅ Test user created!</p>";
        echo "<p>Email: test@example.com<br>Password: password123</p>";
    } else {
        echo "<p style='color: red;'>❌ Error: " . $connection->error . "</p>";
    }
}

echo "<hr>";
if (!isset($_GET['create'])) {
    echo "<p><a href='?create=yes' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none;'>Create Test User</a></p>";
}
echo "<p><a href='/login-simple.php'>Simple Login</a> | <a href='/'>Homepage</a></p>";

$connection->close();
?>