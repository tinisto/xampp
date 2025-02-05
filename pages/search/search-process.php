<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
include $_SERVER["DOCUMENT_ROOT"] . "/pages/search/search-functions.php";

// Check if a search query is set and not empty
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $searchQuery = $_GET['query'];

    // Sanitize the search query (using real_escape_string)
    $sanitizedSearchQuery = mysqli_real_escape_string($connection, $searchQuery);

    // Get the user email if logged in, otherwise set it to null
    $userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;

    // Log the search query
    logSearchQuery($connection, $sanitizedSearchQuery, $userEmail);

    // Optionally send an email notification for the search query
    if (!empty($sanitizedSearchQuery)) {
        $topic = "Search Query";
        $body = "A user searched for: " . $sanitizedSearchQuery;
        if (commonEmail(ADMIN_EMAIL, $topic, $body)) {
            // Email sent successfully (optional: log or handle success)
        } else {
            // Handle email failure (optional)
        }
    }
    $mainContent = 'search-content.php';
    $pageTitle = 'Поиск - 11-классники';
    $additionalData = ['searchQuery' => $sanitizedSearchQuery];
    include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-search.php';
    renderTemplate($pageTitle, $mainContent, $additionalData);
}
