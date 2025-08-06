<?php
echo "<h1>Current Login Page Analysis</h1>";

// Check what login.php actually includes
echo "<h2>What login.php includes:</h2>";
echo "<pre>";
$loginContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/pages/login/login.php');
echo htmlspecialchars($loginContent);
echo "</pre>";

echo "<hr>";

// Check the actual form-template-fixed.php on server
echo "<h2>Current form-template-fixed.php (logo section):</h2>";
$templateContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/includes/form-template-fixed.php');

// Find the logo section
$lines = explode("\n", $templateContent);
$logoSectionFound = false;
foreach ($lines as $i => $line) {
    if (stripos($line, 'logo-section') !== false) {
        echo "<pre>";
        for ($j = max(0, $i-2); $j <= min(count($lines)-1, $i+15); $j++) {
            $lineNum = $j + 1;
            echo sprintf("%3d: %s\n", $lineNum, htmlspecialchars($lines[$j]));
        }
        echo "</pre>";
        $logoSectionFound = true;
        break;
    }
}

if (!$logoSectionFound) {
    echo "<p>Logo section not found in template</p>";
}

echo "<hr>";

// Test the site icon component
echo "<h2>Site Icon Component Test:</h2>";
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/common-components/site-icon.php')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/site-icon.php';
    echo "<p>Gradient version:</p>";
    renderSiteIcon('medium', '/');
} else {
    echo "<p>Site icon component not found</p>";
}
?>