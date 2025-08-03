<?php
// Tracing version to find exact error
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "TRACE: Starting school-single.php<br>\n";

try {
    echo "TRACE: Including check_under_construction.php<br>\n";
    require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
    echo "TRACE: ✓ check_under_construction.php included<br>\n";
    
    echo "TRACE: Including school-single-data-fetch.php<br>\n";
    include 'school-single-data-fetch.php';
    echo "TRACE: ✓ school-single-data-fetch.php included<br>\n";
    
    echo "TRACE: Checking variables:<br>\n";
    echo "- pageTitle: " . (isset($pageTitle) ? $pageTitle : "NOT SET") . "<br>\n";
    echo "- row: " . (isset($row) ? "SET" : "NOT SET") . "<br>\n";
    
    $mainContent = 'pages/school/school-single-content-modern.php';
    $metaD = (isset($pageTitle) ? $pageTitle : '') . ' – образовательное учреждение, предоставляющее высококачественное образование.';
    $metaK = (isset($pageTitle) ? $pageTitle : '') . ', образование, обучение, школьники, 11-классники';
    $additionalData = ['row' => isset($row) ? $row : []];
    
    echo "TRACE: About to include template-engine.php<br>\n";
    echo "TRACE: mainContent = $mainContent<br>\n";
    
    // Try to include template engine
    $templateFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';
    if (!file_exists($templateFile)) {
        die("ERROR: template-engine.php not found at: $templateFile");
    }
    
    include $templateFile;
    
    echo "TRACE: ✓ template-engine.php included<br>\n";
    echo "TRACE: Calling renderTemplate()<br>\n";
    
    // Instead of calling renderTemplate, let's check what would happen
    echo "TRACE: Would call renderTemplate with:<br>\n";
    echo "- pageTitle: $pageTitle<br>\n";
    echo "- mainContent: $mainContent<br>\n";
    echo "- additionalData: " . (count($additionalData) > 0 ? "has data" : "empty") . "<br>\n";
    
    // Check if content file exists
    $contentFile = $_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent;
    echo "TRACE: Content file path: $contentFile<br>\n";
    echo "TRACE: Content file exists: " . (file_exists($contentFile) ? "YES" : "NO") . "<br>\n";
    
    echo "<br>TRACE COMPLETE - If you see this, the error is in renderTemplate() or the content file.<br>\n";
    
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "<br>\n";
    echo "File: " . $e->getFile() . "<br>\n";
    echo "Line: " . $e->getLine() . "<br>\n";
} catch (Error $e) {
    echo "ERROR: " . $e->getMessage() . "<br>\n";
    echo "File: " . $e->getFile() . "<br>\n";
    echo "Line: " . $e->getLine() . "<br>\n";
}
?>