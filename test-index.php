<?php
// Simple test to see if index.php is working
echo "<h1>Test Index</h1>";
echo "<p>This is test-index.php</p>";
echo "<p>Current file: " . __FILE__ . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";

// Test if we can set variables for template
$greyContent1 = '<div style="background: yellow; padding: 20px;"><h1>TEST CONTENT FROM INDEX</h1></div>';
$greyContent2 = '<div style="background: lightblue; padding: 20px;">Test Section 2</div>';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '<div style="background: lightgreen; padding: 20px;">Main content test</div>';
$greyContent6 = '';
$blueContent = '';
$pageTitle = 'Test Page';

echo "<hr>";
echo "<p>About to include real_template.php...</p>";

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>