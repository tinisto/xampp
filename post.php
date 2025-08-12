<?php
// Router for post-new.php
error_reporting(0); // Suppress errors for production

// Include the actual post page logic
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/post/post.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Fallback if post page doesn't exist
    $greyContent1 = '<div style="padding: 30px;"><h1 style="color: #333;">Статья не найдена</h1></div>';
    $greyContent2 = '';
    $greyContent3 = '';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 40px; text-align: center;">
        <h2>Страница временно недоступна</h2>
        <p>Попробуйте позже.</p>
        <p><a href="/" style="color: #007bff;">Вернуться на главную</a></p>
    </div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'Статья не найдена';
    $metaD = '';
    $metaK = '';
}

// Include template - this should always be at the end
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>