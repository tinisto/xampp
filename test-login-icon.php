<?php
// Simple test to see what the form template produces
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/site-icon.php';

echo "<h1>Login Icon Test</h1>";
echo "<p>This should show the gradient version:</p>";
echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 20px 0;'>";

renderSiteIcon('medium', '/');

echo "</div>";

echo "<hr>";
echo "<h2>Full form template test:</h2>";

// Set up the same config as login page
$formConfig = [
    'title' => 'Test Login',
    'action' => '#',
    'submitText' => 'Test'
];
$formFields = null; // Skip fields for this test

echo "<div style='border: 1px solid #ccc; padding: 20px;'>";
include $_SERVER['DOCUMENT_ROOT'] . '/includes/form-template-fixed.php';
echo "</div>";
?>