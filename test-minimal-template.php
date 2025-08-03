<?php
// Test minimal template directly
echo "<!-- DEBUG: Starting template test -->";

// Check if minimal template file exists
$templatePath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-dashboard-minimal.php';
echo "<!-- DEBUG: Template path: $templatePath -->";
echo "<!-- DEBUG: File exists: " . (file_exists($templatePath) ? 'YES' : 'NO') . " -->";

if (!file_exists($templatePath)) {
    die("ERROR: Minimal template file not found at: $templatePath");
}

// Include minimal template
include $templatePath;

// Create simple test content
$testContent = '<h2>Test Dashboard Content</h2><p>This is using the NEW minimal template</p>';

// Render with minimal template
renderDashboardTemplate("Test Dashboard", $testContent);
?>