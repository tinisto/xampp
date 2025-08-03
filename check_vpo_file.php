<?php
// Check what's in the VPO all regions file
echo "<h1>Check VPO All Regions File</h1>";

$file_path = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php';

if (file_exists($file_path)) {
    echo "<h2>File exists at: $file_path</h2>";
    
    // Show first part of the file
    $content = file_get_contents($file_path);
    $lines = explode("\n", $content);
    
    // Find the SQL query line
    echo "<h3>Looking for SQL query around line 190:</h3>";
    echo "<pre>";
    for ($i = 185; $i < min(195, count($lines)); $i++) {
        $line_num = $i + 1;
        echo "Line $line_num: " . htmlspecialchars($lines[$i]) . "\n";
    }
    echo "</pre>";
    
    // Find the count SQL query line
    echo "<h3>Looking for count SQL query around line 197:</h3>";
    echo "<pre>";
    for ($i = 192; $i < min(202, count($lines)); $i++) {
        $line_num = $i + 1;
        echo "Line $line_num: " . htmlspecialchars($lines[$i]) . "\n";
    }
    echo "</pre>";
    
    // Check if fix is needed
    if (strpos($content, 'SELECT region_id') !== false) {
        echo "<p style='color: red;'>❌ File still contains old 'region_id' reference!</p>";
        
        if (isset($_GET['fix']) && $_GET['fix'] == 'yes') {
            // Apply all fixes
            $content = str_replace(
                "SELECT region_id, region_name, region_name_en FROM regions WHERE id_country = 1",
                "SELECT id, region_name, region_name_en FROM regions WHERE country_id = 1",
                $content
            );
            
            $content = str_replace(
                "\$row['region_id']",
                "\$row['id']",
                $content
            );
            
            $content = str_replace(
                "data-region-id=\"<?= \$row['region_id'] ?>\"",
                "data-region-id=\"<?= \$row['id'] ?>\"",
                $content
            );
            
            if (file_put_contents($file_path, $content)) {
                echo "<p style='color: green;'>✅ Fixed the file!</p>";
            }
        } else {
            echo "<p><a href='?fix=yes'>Apply Fix</a></p>";
        }
    } else {
        echo "<p style='color: green;'>✅ File appears to be fixed already</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ File not found!</p>";
}
?>