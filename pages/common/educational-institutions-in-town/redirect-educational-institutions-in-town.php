<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Validate and sanitize input
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Ensure valid input
if ($id <= 0 || !in_array($type, ['schools', 'spo', 'vpo'])) {
    error_log("Invalid input: ID={$id}, Type={$type}, URL={$_SERVER['REQUEST_URI']}");
    header("Location: /404");
    exit();
}

// Initialize variables
$url_slug_town = null;
$id_region = null;

// Query to get town details
$stmt = $connection->prepare("SELECT url_slug_town, id_region FROM towns WHERE id_town = ?");
if ($stmt) {
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $stmt->bind_result($url_slug_town, $id_region);
        $stmt->fetch();
    } else {
        error_log("Error executing statement for town: " . $stmt->error);
    }
    $stmt->close();
}

if ($url_slug_town && $id_region) {
    // Query to get region name
    $region_name_en = null;
    $stmt_region = $connection->prepare("SELECT region_name_en FROM regions WHERE id_region = ?");
    if ($stmt_region) {
        $stmt_region->bind_param("i", $id_region);
        if ($stmt_region->execute()) {
            $stmt_region->bind_result($region_name_en);
            $stmt_region->fetch();
        } else {
            error_log("Error executing statement for region: " . $stmt_region->error);
        }
        $stmt_region->close();
    }

    if ($region_name_en) {
        // Redirect to the clean URL
        $redirect_url = "/$type/$region_name_en/$url_slug_town";
        header("Location: $redirect_url", true, 301);
        exit();
    }
}

// If any part fails, log an error and redirect to 404
error_log("Redirect failed: ID={$id}, Type={$type}, URL={$_SERVER['REQUEST_URI']}");
header("Location: /404");
exit();

// Close the database connection
$connection->close();
