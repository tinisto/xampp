<?php
// This file will help identify which script is being executed
echo "<!-- IDENTIFY-HOMEPAGE-FILE -->\n";
echo "<!-- Current script: " . $_SERVER['SCRIPT_FILENAME'] . " -->\n";
echo "<!-- PHP_SELF: " . $_SERVER['PHP_SELF'] . " -->\n";
echo "<!-- REQUEST_URI: " . $_SERVER['REQUEST_URI'] . " -->\n";

// Add a marker to real_components.php to see if it's being served
$marker = "\n<!-- REAL_COMPONENTS_MARKER: This is real_components.php being served -->\n";
$realComponentsPath = $_SERVER['DOCUMENT_ROOT'] . '/real_components.php';

// Read the file
$content = file_get_contents($realComponentsPath);

// Check if marker already exists
if (strpos($content, 'REAL_COMPONENTS_MARKER') === false) {
    // Add marker at the beginning after <?php
    $content = str_replace('<?php', '<?php' . $marker, $content);
    file_put_contents($realComponentsPath, $content);
    echo "<p>✓ Added marker to real_components.php</p>";
} else {
    echo "<p>ℹ️ Marker already exists in real_components.php</p>";
}

// Do the same for real_template.php
$templateMarker = "\n<!-- REAL_TEMPLATE_MARKER: This is real_template.php being served -->\n";
$realTemplatePath = $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
$templateContent = file_get_contents($realTemplatePath);

if (strpos($templateContent, 'REAL_TEMPLATE_MARKER') === false) {
    $templateContent = str_replace('<?php', '<?php' . $templateMarker, $templateContent);
    file_put_contents($realTemplatePath, $templateContent);
    echo "<p>✓ Added marker to real_template.php</p>";
} else {
    echo "<p>ℹ️ Marker already exists in real_template.php</p>";
}

echo "<p>Now visit https://11klassniki.ru/ and view page source to see which marker appears.</p>";
?>