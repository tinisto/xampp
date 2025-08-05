<?php
// Working version of post.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get URL parameter
$url_post = isset($_GET['url_post']) ? mysqli_real_escape_string($connection, $_GET['url_post']) : '';

if (empty($url_post)) {
    // Log the error
    error_log("Post error: Empty url_post parameter. Request URI: " . $_SERVER['REQUEST_URI']);
    header("Location: /404");
    exit();
}

// Fetch post data
$query = "SELECT * FROM posts WHERE url_slug = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $url_post);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $pageTitle = $row['title_post'];
    $postData = $row;
    $rowPost = $row; // For post-content.php
    $metaD = $row['meta_d_post'] ?? '';
    $metaK = $row['meta_k_post'] ?? '';
} else {
    // Log which post was not found
    error_log("Post not found in database: " . $url_post);
    header("Location: /404");
    exit();
}

mysqli_stmt_close($stmt);

// Start output
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 11 Классники</title>
    <meta name="description" content="<?= htmlspecialchars($metaD) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($metaK) ?>">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/css-variables.css">
    <link rel="stylesheet" href="/css/theme-controller.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
            padding: 20px 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
    </style>
</head>
<body>
    <?php 
    // Include header without any construction checks
    $headerFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php';
    if (file_exists($headerFile)) {
        // Temporarily disable error reporting for header
        $oldLevel = error_reporting(0);
        include $headerFile;
        error_reporting($oldLevel);
    } else {
        echo '<header style="background: #28a745; color: white; padding: 20px 0; text-align: center;"><h1>11 Классники</h1></header>';
    }
    ?>
    
    <main>
        <div class="container">
            <?php 
            // Include post content
            $contentFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/post/post-content.php';
            if (file_exists($contentFile)) {
                include $contentFile;
            } else {
                // Fallback content
                echo '<article>';
                echo '<h1>' . htmlspecialchars($pageTitle) . '</h1>';
                echo '<div class="post-meta">';
                echo '<span>Дата: ' . date('d.m.Y', strtotime($row['date_post'])) . '</span>';
                echo '<span> | Просмотров: ' . number_format($row['view_post']) . '</span>';
                echo '</div>';
                echo '<div class="post-body">';
                echo nl2br(htmlspecialchars($row['text_post']));
                echo '</div>';
                echo '</article>';
            }
            ?>
        </div>
    </main>
    
    <?php 
    // Include footer
    $footerFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php';
    if (file_exists($footerFile)) {
        $oldLevel = error_reporting(0);
        include $footerFile;
        error_reporting($oldLevel);
    } else {
        echo '<footer style="background: #333; color: white; text-align: center; padding: 20px 0; margin-top: 40px;"><p>&copy; 2024 11 Классники</p></footer>';
    }
    ?>
</body>
</html>