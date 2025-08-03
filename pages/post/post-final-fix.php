<?php
// Fixed post page that uses the existing database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection from the existing file
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get URL parameter
$url_post = isset($_GET['url_post']) ? mysqli_real_escape_string($connection, $_GET['url_post']) : '';

if (empty($url_post)) {
    header("Location: /404");
    exit();
}

// Fetch post data
$query = "SELECT * FROM posts WHERE url_post = ?";
$stmt = mysqli_prepare($connection, $query);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($connection));
}

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
    header("Location: /404");
    exit();
}

mysqli_stmt_close($stmt);
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
        /* Ensure proper layout */
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
    // Include header safely
    $headerFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php';
    if (file_exists($headerFile)) {
        // Save current error reporting
        $currentError = error_reporting();
        error_reporting(0);
        
        include $headerFile;
        
        // Restore error reporting
        error_reporting($currentError);
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
                echo "<h1>" . htmlspecialchars($pageTitle) . "</h1>";
                echo "<p>Content file not found.</p>";
            }
            ?>
        </div>
    </main>
    
    <?php 
    // Include footer safely
    $footerFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php';
    if (file_exists($footerFile)) {
        include $footerFile;
    }
    ?>
</body>
</html>