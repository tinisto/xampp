<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/address-selection.php';

// Get the country ID from the query parameters
$countryId = isset($_GET['country_id']) ? $_GET['country_id'] : null;

// Check if the country ID is provided
if (!$countryId) {
    // If the country ID is missing, return an error message in JSON format
    echo json_encode(['error' => 'Country ID is missing']);
    exit();  // Stop further execution
}

// Fetch regions based on the country ID
try {
    $regions = getRegionsByCountry($countryId);

    // If no regions are found for the given country
    if (empty($regions)) {
        // Return an error message in JSON format
        echo json_encode(['error' => 'No regions found for this country']);
        exit();  // Stop further execution
    }

    // Return the regions as JSON
    header('Content-Type: application/json');
    echo json_encode($regions);
} catch (Exception $e) {
    // Handle any exceptions and return an error message in JSON format
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
    exit();  // Stop further execution
}
