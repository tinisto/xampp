<?php
// Direct test file - 2025-08-07 22:28:11.010727
echo "<!-- DIRECT TEST FILE -->";
echo "<h1>This is a direct test</h1>";
$greyContent1 = '<div style="background: red; padding: 40px;"><h1>DIRECT TEST WORKING</h1></div>';
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '<div style="background: yellow; padding: 40px;">If you see this, the file is working!</div>';
$greyContent6 = '';
$blueContent = '';
$pageTitle = 'Direct Test';
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>