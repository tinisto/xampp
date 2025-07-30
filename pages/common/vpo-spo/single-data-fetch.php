<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Get the requested URL path
$requestPath = $_SERVER['REQUEST_URI'];

// Split the path into segments
$pathSegments = explode('/', trim($requestPath, '/'));

// Determine the type (vpo or spo) based on the URL
$type = $pathSegments[0];
$urlField = $type === 'vpo' ? 'vpo_url' : 'spo_url';
$table = $type === 'vpo' ? 'vpo' : 'spo';
$nameField = $type === 'vpo' ? 'vpo_name' : 'spo_name';
$metaDField = $type === 'vpo' ? 'meta_d_vpo' : 'meta_d_spo';
$metaKField = $type === 'vpo' ? 'meta_k_vpo' : 'meta_k_spo';

// Check if the URL matches the expected structure
if (count($pathSegments) >= 2 && ($type === 'vpo' || $type === 'spo')) {
    $url = isset($pathSegments[1]) ? $pathSegments[1] : null;
} else {
    $url = null;
}

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
    // Select data using prepared statements with JOIN to get city name
    $query = "SELECT $table.*, towns.name as city FROM $table LEFT JOIN towns ON $table.id_town = towns.id_town WHERE $urlField=? AND approved='1'";
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
