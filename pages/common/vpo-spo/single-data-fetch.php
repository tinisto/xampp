<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Get the URL from GET parameter (set by rewrite rule) or from path
if (isset($_GET['vpo_url'])) {
    $url = $_GET['vpo_url'];
    $type = 'vpo';
} elseif (isset($_GET['spo_url'])) {
    $url = $_GET['spo_url'];
    $type = 'spo';
} else {
    // Fallback to parsing from path
    $requestPath = $_SERVER['REQUEST_URI'];
    $pathSegments = explode('/', trim($requestPath, '/'));
    
    // Determine the type (vpo or spo) based on the URL
    $type = $pathSegments[0];
    
    // Check if the URL matches the expected structure
    if (count($pathSegments) >= 2 && ($type === 'vpo' || $type === 'spo')) {
        $url = isset($pathSegments[1]) ? $pathSegments[1] : null;
    } else {
        $url = null;
    }
}

// Set the field names based on type
$urlField = $type === 'vpo' ? 'vpo_url' : 'spo_url';
$table = $type === 'vpo' ? 'vpo' : 'spo';
$nameField = $type === 'vpo' ? 'vpo_name' : 'spo_name';
$metaDField = $type === 'vpo' ? 'meta_d_vpo' : 'meta_d_spo';
$metaKField = $type === 'vpo' ? 'meta_k_vpo' : 'meta_k_spo';

if (!isset($_SESSION['visited'])) {
    if ($url) {
        // Update the view count using prepared statements
        $updateViewQuery = "UPDATE $table SET view = view + 1 WHERE $urlField=?";
        $stmtUpdateView = mysqli_prepare($connection, $updateViewQuery);
        mysqli_stmt_bind_param($stmtUpdateView, "s", $url);
        mysqli_stmt_execute($stmtUpdateView);
        mysqli_stmt_close($stmtUpdateView);
    }
    $_SESSION['visited'] = true;
}

if ($url) {
    // Select data using prepared statements  
    $query = "SELECT * FROM $table WHERE $urlField=?";
    $stmtSelect = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmtSelect, "s", $url);
    mysqli_stmt_execute($stmtSelect);
    $result = mysqli_stmt_get_result($stmtSelect);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        
        if (isset($row[$nameField])) {
            $pageTitle = $row[$nameField];
            $metaD = $row[$metaDField];
            $metaK = $row[$metaKField];
        } else {
            header("Location: /404");
            exit();
        }
        mysqli_stmt_close($stmtSelect);
    } else {
        header("Location: /404");
        exit();
    }
} else {
    header("Location: /404");
    exit();
}
?>
