<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// First check for GET parameters (from .htaccess rewrite rules)
if (isset($_GET['vpo_url'])) {
    $type = 'vpo';
    $url = $_GET['vpo_url'];
} elseif (isset($_GET['spo_url'])) {
    $type = 'spo';
    $url = $_GET['spo_url'];
} else {
    // Fallback to URL path parsing (for direct access)
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

// Set field names based on type - using new database structure
$urlField = 'url_slug';
$table = $type === 'vpo' ? 'universities' : 'colleges';
$nameField = $type === 'vpo' ? 'university_name' : 'college_name';
$metaDField = 'meta_description';
$metaKField = 'meta_keywords';
$viewField = 'view_count';
$approvedField = 'is_approved';

if (!isset($_SESSION['visited'])) {
    if ($url) {
        // Update the view count using prepared statements
        $updateViewQuery = "UPDATE $table SET $viewField = $viewField + 1 WHERE $urlField=?";
        $stmtUpdateView = mysqli_prepare($connection, $updateViewQuery);
        mysqli_stmt_bind_param($stmtUpdateView, "s", $url);
        mysqli_stmt_execute($stmtUpdateView);
        mysqli_stmt_close($stmtUpdateView);
    }
    $_SESSION['visited'] = true;
}

if ($url) {
    // Select data using prepared statements with JOIN to get city name
    $query = "SELECT $table.*, towns.town_name as city FROM $table LEFT JOIN towns ON $table.town_id = towns.id WHERE $urlField=? AND $approvedField='1'";
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
