<?php
// News page with error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set default content first
$greyContent1 = '<div style="padding: 30px;"><h1>Новости</h1></div>';
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '<div style="padding: 20px;">Loading news...</div>';
$greyContent6 = '';
$blueContent = '';
$pageTitle = 'Новости';

// Try to include the actual news page
$originalFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news.php';
if (file_exists($originalFile)) {
    // Capture any output
    ob_start();
    $includeError = false;
    
    try {
        // The included file should override our default variables
        include $originalFile;
    } catch (Exception $e) {
        $includeError = true;
        $greyContent5 = '<div style="background: #fee; padding: 20px; color: #c00;">Error loading news: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    
    $output = ob_get_clean();
    
    // If there was output, it might be an error
    if (!empty($output) && $includeError) {
        $greyContent5 = '<div style="background: #fee; padding: 20px; color: #c00;">Error: ' . htmlspecialchars($output) . '</div>';
    }
} else {
    $greyContent5 = '<div style="background: #fee; padding: 20px; color: #c00;">News page not found at: ' . $originalFile . '</div>';
}

// Make sure we have the template
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/real_template.php')) {
    die('Template not found!');
}
?>