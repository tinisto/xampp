<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Determine the type (vpo or spo) based on the URL
$requestUri = $_SERVER['REQUEST_URI'];
$type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
$idField = $type === 'vpo' ? 'id_vpo' : 'id_spo';
$urlField = $type === 'vpo' ? 'vpo_url' : 'spo_url';
$table = $type === 'vpo' ? 'vpo' : 'spo';
$idParam = $type === 'vpo' ? 'id_university' : 'id_college';

// Get id_university/id_college and id from query string
$id_param = isset($_GET[$idParam]) ? intval($_GET[$idParam]) : 0;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Determine which ID to use for id_vpo/id_spo
if ($id_param > 0) {
    $id_field = $id_param;
} elseif ($id > 0) {
    $id_field = $id;
} else {
    $id_field = 0;
}

if ($id_field > 0) {
    // Prepare and bind for id_vpo/id_spo
    $stmt = $connection->prepare("SELECT $urlField FROM $table WHERE $idField = ?");
    if ($stmt === false) {
        error_log("Error preparing statement: " . $connection->error);
        header("Location: /404");
        exit();
    }

    $stmt->bind_param("i", $id_field);

    // Execute the query
    if (!$stmt->execute()) {
        error_log("Error executing statement: " . $stmt->error);
        header("Location: /404");
        exit();
    }

    // Bind the result
    $stmt->bind_result($url);

    // Fetch the result
    if ($stmt->fetch()) {
        // Redirect to the clean URL
        header("Location: /$type/$url", true, 301);
        exit();
    } else {
        // Handle the case where no url is found
        header("Location: /404");
        exit();
    }

    // Close the statement
    $stmt->close();
} else {
    header("Location: /404");
    exit();
}

// Close the connection
$connection->close();
?>
