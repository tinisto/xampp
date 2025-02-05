<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Get id_region from query string
$id_region = isset($_GET['id_region']) ? intval($_GET['id_region']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'spo';

if ($id_region > 0) {
    // Prepare and bind
    $stmt = $connection->prepare("SELECT region_name_en FROM regions WHERE id_region = ?");
    if ($stmt === false) {
        error_log("Error preparing statement: " . $connection->error);
        header("Location: /404");
        exit();
    }

    $stmt->bind_param("i", $id_region);

    // Execute the query
    if (!$stmt->execute()) {
        error_log("Error executing statement: " . $stmt->error);
        header("Location: /404");
        exit();
    }

    // Bind the result
    $stmt->bind_result($region_name_en);

    // Fetch the result
    if ($stmt->fetch()) {
        // Redirect to the clean URL
        header("Location: /$type-in-region/$region_name_en", true, 301);
        exit();
    } else {
        // Handle the case where no region_name_en is found
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
