<?php
// Fixed post page without construction check
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database connection directly
if (!isset($connection)) {
    $connection = mysqli_connect("localhost", "11klassniki_claude", "2xErkKSPaAAqpMt", "11klassniki_claude");
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }
    mysqli_set_charset($connection, "utf8");
}

// Get URL parameter
$url_post = isset($_GET['url_post']) ? mysqli_real_escape_string($connection, $_GET['url_post']) : '';

if (empty($url_post)) {
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
</head>
<body>
    <?php 
    // Include header without construction check
    $headerFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php';
    if (file_exists($headerFile)) {
        // Temporarily suppress any includes in header that might cause issues
        ob_start();
        include $headerFile;
        $headerContent = ob_get_clean();
        echo $headerContent;
    }
    ?>
    
    <main>
        <div class="container">
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/pages/post/post-content.php'; ?>
        </div>
    </main>
    
    <?php 
    // Include footer
    $footerFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php';
    if (file_exists($footerFile)) {
        include $footerFile;
    }
    ?>
</body>
</html>