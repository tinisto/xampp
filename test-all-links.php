<?php
// Comprehensive site link testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head>";
echo "<title>Complete Site Link Test</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .test-section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { color: #28a745; }
    .error { color: #dc3545; }
    .warning { color: #ffc107; }
    h1, h2 { color: #333; }
    .status { padding: 4px 8px; border-radius: 4px; font-weight: bold; }
    .status.ok { background: #d4edda; color: #155724; }
    .status.fail { background: #f8d7da; color: #721c24; }
    .status.warn { background: #fff3cd; color: #856404; }
</style>";
echo "</head><body>";

echo "<h1>🔍 Complete Site Link Test</h1>";

// Test main navigation pages
$mainPages = [
    '/' => 'Homepage',
    '/schools.php' => 'Schools Directory',
    '/spo.php' => 'SPO Directory',
    '/vpo.php' => 'VPO Directory',
    '/news.php' => 'News',
    '/posts.php' => 'Posts',
    '/about.php' => 'About',
    '/contact.php' => 'Contact',
    '/privacy.php' => 'Privacy Policy',
    '/terms.php' => 'Terms of Service',
];

echo "<div class='test-section'>";
echo "<h2>📊 Main Navigation Pages</h2>";
foreach ($mainPages as $url => $title) {
    $file = $_SERVER['DOCUMENT_ROOT'] . ($url === '/' ? '/index.php' : $url);
    echo "<h3>$title ($url)</h3>";
    
    if (file_exists($file)) {
        echo "<span class='status ok'>✅ File exists</span><br>";
        
        // Check PHP syntax
        $output = shell_exec("php -l \"$file\" 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "<span class='status ok'>✅ PHP syntax OK</span><br>";
        } else {
            echo "<span class='status fail'>❌ PHP syntax error:</span> " . htmlspecialchars($output) . "<br>";
        }
        
        // Try to render
        ob_start();
        try {
            include $file;
            $content = ob_get_contents();
            if (!empty($content)) {
                echo "<span class='status ok'>✅ Renders content (" . strlen($content) . " chars)</span><br>";
            } else {
                echo "<span class='status warn'>⚠️ Renders but no visible content</span><br>";
            }
        } catch (Exception $e) {
            echo "<span class='status fail'>❌ Runtime error:</span> " . htmlspecialchars($e->getMessage()) . "<br>";
        } catch (Error $e) {
            echo "<span class='status fail'>❌ Fatal error:</span> " . htmlspecialchars($e->getMessage()) . "<br>";
        }
        ob_end_clean();
        
    } else {
        echo "<span class='status fail'>❌ File missing</span><br>";
    }
    echo "<hr>";
}
echo "</div>";

// Test educational institution pages
echo "<div class='test-section'>";
echo "<h2>🏫 Educational Institution Pages</h2>";

$educationPages = [
    '/schools.php?type=schools' => 'Schools All Regions',
    '/spo.php?type=spo' => 'SPO All Regions', 
    '/vpo.php?type=vpo' => 'VPO All Regions',
];

foreach ($educationPages as $url => $title) {
    echo "<h3>$title ($url)</h3>";
    
    // Parse URL to get file and query
    $urlParts = parse_url($url);
    $file = $_SERVER['DOCUMENT_ROOT'] . $urlParts['path'];
    
    if (file_exists($file)) {
        echo "<span class='status ok'>✅ File exists</span><br>";
        
        // Set query parameters for testing
        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $_GET);
        }
        
        // Try to render with database connection
        ob_start();
        try {
            include $file;
            $content = ob_get_contents();
            if (!empty($content)) {
                echo "<span class='status ok'>✅ Renders content (" . strlen($content) . " chars)</span><br>";
            } else {
                echo "<span class='status warn'>⚠️ Renders but no visible content</span><br>";
            }
        } catch (Exception $e) {
            echo "<span class='status fail'>❌ Runtime error:</span> " . htmlspecialchars($e->getMessage()) . "<br>";
        } catch (Error $e) {
            echo "<span class='status fail'>❌ Fatal error:</span> " . htmlspecialchars($e->getMessage()) . "<br>";
        }
        ob_end_clean();
        
        // Reset GET parameters
        $_GET = [];
        
    } else {
        echo "<span class='status fail'>❌ File missing</span><br>";
    }
    echo "<hr>";
}
echo "</div>";

// Test header navigation links
echo "<div class='test-section'>";
echo "<h2>🧭 Header Navigation Links</h2>";

$headerFile = $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php';
if (file_exists($headerFile)) {
    $headerContent = file_get_contents($headerFile);
    
    // Extract href links from header
    preg_match_all('/href="([^"]+)"/', $headerContent, $matches);
    $headerLinks = array_unique($matches[1]);
    
    foreach ($headerLinks as $link) {
        if (strpos($link, 'http') === 0 || strpos($link, '#') === 0) continue; // Skip external and anchor links
        
        echo "<h4>Header Link: $link</h4>";
        $file = $_SERVER['DOCUMENT_ROOT'] . $link;
        
        if (file_exists($file)) {
            echo "<span class='status ok'>✅ Target file exists</span><br>";
        } else {
            echo "<span class='status fail'>❌ Target file missing: $file</span><br>";
        }
        echo "<hr>";
    }
} else {
    echo "<span class='status fail'>❌ Header file not found</span><br>";
}
echo "</div>";

// Test footer navigation links  
echo "<div class='test-section'>";
echo "<h2>🦶 Footer Navigation Links</h2>";

$footerFile = $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php';
if (file_exists($footerFile)) {
    $footerContent = file_get_contents($footerFile);
    
    // Extract href links from footer
    preg_match_all('/href="([^"]+)"/', $footerContent, $matches);
    $footerLinks = array_unique($matches[1]);
    
    foreach ($footerLinks as $link) {
        if (strpos($link, 'http') === 0 || strpos($link, '#') === 0) continue; // Skip external and anchor links
        
        echo "<h4>Footer Link: $link</h4>";
        $file = $_SERVER['DOCUMENT_ROOT'] . $link;
        
        if (file_exists($file)) {
            echo "<span class='status ok'>✅ Target file exists</span><br>";
        } else {
            echo "<span class='status fail'>❌ Target file missing: $file</span><br>";
        }
        echo "<hr>";
    }
} else {
    echo "<span class='status fail'>❌ Footer file not found</span><br>";
}
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>✅ Test Complete</h2>";
echo "<p>Site link testing completed. Review results above for any issues that need attention.</p>";
echo "</div>";

echo "</body></html>";
?>