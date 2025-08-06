<?php
echo "<h1>Finding Login Template</h1>";

// Check what the login.php file contains
$loginPath = $_SERVER['DOCUMENT_ROOT'] . '/pages/login/login.php';
echo "<h2>Login.php content:</h2>";
echo "<pre>";
if (file_exists($loginPath)) {
    echo htmlspecialchars(file_get_contents($loginPath));
} else {
    echo "Login file not found!";
}
echo "</pre>";

echo "<hr>";

// Check what templates exist with SVG
echo "<h2>Searching for templates with SVG:</h2>";
$templates = [
    '/includes/form-template.php',
    '/includes/form-template-fixed.php',
    '/pages/login/login-template.php',
    '/pages/registration/registration_template.php'
];

foreach ($templates as $template) {
    $path = $_SERVER['DOCUMENT_ROOT'] . $template;
    echo "<h3>$template:</h3>";
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (strpos($content, 'svg') !== false || strpos($content, 'circle') !== false) {
            echo "<p style='color: red;'>✗ Contains SVG code - this might be the culprit!</p>";
            
            // Show the SVG section
            $lines = explode("\n", $content);
            $svgFound = false;
            foreach ($lines as $i => $line) {
                if (stripos($line, 'svg') !== false || stripos($line, 'circle') !== false) {
                    echo "<pre>";
                    for ($j = max(0, $i-2); $j <= min(count($lines)-1, $i+5); $j++) {
                        echo ($j+1) . ": " . htmlspecialchars($lines[$j]) . "\n";
                    }
                    echo "</pre>";
                    $svgFound = true;
                    break;
                }
            }
        } else {
            echo "<p style='color: green;'>✓ No SVG found</p>";
        }
    } else {
        echo "<p>File not found</p>";
    }
}
?>