<?php
echo "<h2>Find All Login Process Files</h2>";

function findFiles($dir, $pattern) {
    $files = [];
    if (is_dir($dir)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile() && preg_match($pattern, $file->getFilename())) {
                $files[] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file->getPathname());
            }
        }
    }
    return $files;
}

$loginFiles = findFiles($_SERVER['DOCUMENT_ROOT'], '/login.*process.*\.php$/i');

echo "<h3>Login Process Files Found:</h3>";
foreach ($loginFiles as $file) {
    echo "<strong>$file</strong><br>";
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;
    echo "Last modified: " . date('Y-m-d H:i:s', filemtime($fullPath)) . "<br>";
    echo "Size: " . filesize($fullPath) . " bytes<br>";
    
    $content = file_get_contents($fullPath);
    if (strpos($content, 'strpos($redirect, \'/\') === 0') !== false) {
        echo "<span style='color: green;'>✓ Contains new redirect logic</span><br>";
    } else {
        echo "<span style='color: red;'>✗ Missing new redirect logic</span><br>";
    }
    echo "<br>";
}

echo "<h3>Check Login Form Action</h3>";
$loginForm = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/login-modern.php');
if (preg_match('/action=["\']([^"\']+)["\']/', $loginForm, $matches)) {
    echo "Login form action: " . $matches[1] . "<br>";
} else {
    echo "Could not find form action<br>";
}

echo "<h3>All PHP files in /pages/login/:</h3>";
$loginDir = $_SERVER['DOCUMENT_ROOT'] . '/pages/login/';
if (is_dir($loginDir)) {
    $files = scandir($loginDir);
    foreach ($files as $file) {
        if (strpos($file, '.php') !== false) {
            echo "$file (" . filesize($loginDir . $file) . " bytes)<br>";
        }
    }
} else {
    echo "Directory not found<br>";
}
?>