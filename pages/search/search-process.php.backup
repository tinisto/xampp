<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
include $_SERVER["DOCUMENT_ROOT"] . "/pages/search/search-functions.php";

// Check if a search query is set and not empty
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $searchQuery = trim($_GET['query']);

    // ✅ FIX: Allow Cyrillic letters, Latin letters, numbers, and spaces
    if (!preg_match("/^[\p{L}0-9\s]+$/u", $searchQuery)) {
        // Notify admin about a suspicious search attempt
        $topic = "Suspicious Search Attempt";
        $body = "A suspicious search query was detected: " . htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8');
        commonEmail(ADMIN_EMAIL, $topic, $body);

        die("Invalid search input.");
    }

    // Blacklist check to prevent SQL injection attempts with common malicious keywords
    $blacklist = ['sleep', 'waitfor', 'DBMS_PIPE', 'sysdate', 'select', 'union', '--', '#', 'or 1=1'];

    foreach ($blacklist as $badWord) {
        if (stripos($searchQuery, $badWord) !== false) {
            // Notify admin about the suspicious search attempt
            $topic = "Suspicious Search Attempt";
            $body = "A suspicious search query containing blacklisted terms was detected: " . htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8');
            commonEmail(ADMIN_EMAIL, $topic, $body);

            die("Suspicious activity detected.");
        }
    }

    // Sanitize the search query
    $sanitizedSearchQuery = mysqli_real_escape_string($connection, $searchQuery);

    // Get the user email if logged in, otherwise set it to null
    $userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;

    // Log the search query
    logSearchQuery($connection, $sanitizedSearchQuery, $userEmail);

    // Optionally send an email notification for normal search queries
    if (!empty($sanitizedSearchQuery)) {
        $topic = "Search Query";
        $body = "A user searched for: " . htmlspecialchars($sanitizedSearchQuery, ENT_QUOTES, 'UTF-8');
        commonEmail(ADMIN_EMAIL, $topic, $body);
    }

    $mainContent = 'search-content.php';
    $pageTitle = 'Поиск - 11-классники';
    $additionalData = ['searchQuery' => $sanitizedSearchQuery];

    include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-search.php';
    renderTemplate($pageTitle, $mainContent, $additionalData);
}
