<?php
// Router for post-new.php
error_reporting(0); // Suppress errors for production

// Set default content
$greyContent1 = '<div style="padding: 30px;"><h1>Статья</h1></div>';
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '<div style="padding: 20px;"><p>Loading...</p></div>';
$greyContent6 = '';
$blueContent = '';
$pageTitle = 'Статья';
$metaD = '';
$metaK = '';

// Try to include the actual page
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/post/post.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    $greyContent5 = '<div style="padding: 40px; text-align: center;">
        <h2>Page temporarily unavailable</h2>
        <p>Please try again later.</p>
        <p><a href="/" style="color: #28a745;">Return to homepage</a></p>
    </div>';
}

// Ensure template exists
if (!isset($greyContent1)) {
    $greyContent1 = '<div style="padding: 30px;"><h1>Статья</h1></div>';
}

// Include template - this should always be at the end
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/real_template.php')) {
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
} else {
    echo "Template not found";
}
?>