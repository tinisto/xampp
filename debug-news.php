<?php
// Debug news page
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!-- DEBUG NEWS PAGE -->\n";

// Simple test without database
$greyContent1 = '<div style="padding: 20px; background: yellow;"><h1>NEWS PAGE TEST</h1></div>';
$greyContent2 = '<div style="padding: 20px;">Category navigation would go here</div>';
$greyContent3 = '';
$greyContent4 = '<div style="padding: 20px;">Filters would go here</div>';
$greyContent5 = '<div style="padding: 20px; background: lightblue;"><h2>News articles would go here</h2></div>';
$greyContent6 = '<div style="padding: 20px;">Pagination would go here</div>';
$blueContent = '';
$pageTitle = 'News Test';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>