<?php
echo "<h2>MySQL Connection Diagnostics</h2>";

// Check if MySQL socket exists
$sockets = [
    '/tmp/mysql.sock',
    '/var/mysql/mysql.sock', 
    '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock',
    '/Applications/XAMPP/xamppfiles/tmp/mysql.sock'
];

echo "<h3>Checking MySQL Socket Files:</h3>";
foreach ($sockets as $socket) {
    echo $socket . ": " . (file_exists($socket) ? "<span style='color:green'>EXISTS</span>" : "<span style='color:red'>NOT FOUND</span>") . "<br>";
}

// Try different connection methods
echo "<h3>Testing Connection Methods:</h3>";

// Method 1: Using 127.0.0.1
echo "<h4>Method 1: TCP/IP (127.0.0.1)</h4>";
try {
    $conn1 = new mysqli('127.0.0.1', 'root', 'root', '11klassniki_claude');
    if ($conn1->connect_error) {
        echo "<span style='color:red'>Failed: " . $conn1->connect_error . "</span><br>";
    } else {
        echo "<span style='color:green'>Success!</span><br>";
        $conn1->close();
    }
} catch (Exception $e) {
    echo "<span style='color:red'>Exception: " . $e->getMessage() . "</span><br>";
}

// Method 2: Using localhost with specific socket
echo "<h4>Method 2: Socket (localhost with socket path)</h4>";
$socketPath = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';
if (file_exists($socketPath)) {
    try {
        ini_set('mysqli.default_socket', $socketPath);
        $conn2 = new mysqli('localhost', 'root', 'root', '11klassniki_claude');
        if ($conn2->connect_error) {
            echo "<span style='color:red'>Failed: " . $conn2->connect_error . "</span><br>";
        } else {
            echo "<span style='color:green'>Success!</span><br>";
            $conn2->close();
        }
    } catch (Exception $e) {
        echo "<span style='color:red'>Exception: " . $e->getMessage() . "</span><br>";
    }
} else {
    echo "<span style='color:orange'>Socket file not found at: $socketPath</span><br>";
}

// Check XAMPP paths
echo "<h3>XAMPP Installation Check:</h3>";
$xamppPaths = [
    '/Applications/XAMPP/xamppfiles/bin/mysql',
    '/Applications/XAMPP/xamppfiles/var/mysql',
    '/Applications/XAMPP/xamppfiles/etc/my.cnf'
];

foreach ($xamppPaths as $path) {
    echo $path . ": " . (file_exists($path) ? "<span style='color:green'>EXISTS</span>" : "<span style='color:red'>NOT FOUND</span>") . "<br>";
}

// Check if we can read MySQL config
$configFile = '/Applications/XAMPP/xamppfiles/etc/my.cnf';
if (file_exists($configFile)) {
    echo "<h4>MySQL Config (my.cnf) - Socket Settings:</h4>";
    $config = file_get_contents($configFile);
    if (preg_match('/socket\s*=\s*(.+)$/m', $config, $matches)) {
        echo "Socket path in config: " . trim($matches[1]) . "<br>";
    }
}

// System info
echo "<h3>System Information:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Operating System: " . php_uname() . "<br>";
?>