<?php
echo "<h1>Debug Form Template</h1>";
echo "<p>Checking if site-icon.php exists:</p>";

$iconPath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/site-icon.php';
if (file_exists($iconPath)) {
    echo "<p style='color: green;'>✓ site-icon.php exists at: $iconPath</p>";
    
    include_once $iconPath;
    
    echo "<h2>Testing renderSiteIconSVG function:</h2>";
    if (function_exists('renderSiteIconSVG')) {
        echo "<p style='color: green;'>✓ renderSiteIconSVG function exists</p>";
        echo "<div style='border: 1px solid #ccc; padding: 10px;'>";
        renderSiteIconSVG('medium', '/');
        echo "</div>";
    } else {
        echo "<p style='color: red;'>✗ renderSiteIconSVG function not found</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ site-icon.php not found</p>";
}

echo "<h2>Testing form template include:</h2>";
$formTemplatePath = $_SERVER['DOCUMENT_ROOT'] . '/includes/form-template-fixed.php';
if (file_exists($formTemplatePath)) {
    echo "<p style='color: green;'>✓ form-template-fixed.php exists</p>";
} else {
    echo "<p style='color: red;'>✗ form-template-fixed.php not found</p>";
}
?>