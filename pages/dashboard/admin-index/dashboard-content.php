<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

$basePaths = [
    "school" => __DIR__ . '/../schools-dashboard/schools-index-cards/',
    "vpo" => __DIR__ . '/../vpo-dashboard/vpo-index-cards/',
    "spo" => __DIR__ . '/../spo-dashboard/spo-index-cards/',
    "search" => __DIR__ . '/../search-dashboard/search-index-cards/',
    "users" => __DIR__ . '/../users-dashboard/users-index-cards/',
    "comments" => __DIR__ . '/../comments-dashboard/comments-index-cards/',
    "messages" => __DIR__ . '/../messages-dashboard/messages-index-cards/',
    "news" => __DIR__ . '/../news-dashboard/news-index-cards/',
    "posts" => __DIR__ . '/../posts-dashboard/posts-index-cards/',
];

// Example card groups with card names
$cardGroups = [
    ["school-create-card.php", "school-approve-edit-card.php", "school-approve-new-card.php", "school-view-card.php"],
    ["vpo-create-card.php", "vpo-edit-card.php", "vpo-new-card.php", "vpo-view-card.php"],
    ["spo-create-card.php", "spo-edit-card.php", "spo-new-card.php", "spo-view-card.php"],
    ["search-empty-card.php", "search-empty-card.php", "search-empty-card.php", "search-view-card.php"],
    ["users-empty-card.php", "users-empty-card.php", "users-empty-card.php", "users-view-card.php"],
    ["comments-empty-card.php", "comments-empty-card.php", "comments-empty-card.php", "comments-view-card.php"],
    ["messages-empty-card.php", "messages-empty-card.php", "messages-empty-card.php", "messages-view-card.php"],
    ["news-create-card.php", "news-empty-card.php", "news-approve-card.php", "news-view-card.php"],
    ["posts-create-card.php", "posts-empty-card.php", "posts-empty-card.php", "posts-view-card.php"],

];

foreach ($cardGroups as $index => $cards) {
    switch ($index) {
        case 0:
            $type = 'school';
            break;
        case 1:
            $type = 'vpo';
            break;
        case 2:
            $type = 'spo';
            break;
        case 3:
            $type = 'search';
            break;
        case 4:
            $type = 'users';
            break;
        case 5:
            $type = 'comments';
            break;
        case 6:
            $type = 'messages';
            break;
        case 7:
            $type = 'news';
            break;
        case 8:
            $type = 'posts';
            break;
    }

    // Output the cards within a row for each type
    echo '<div class="row d-flex mt-4 gap-2">';
    foreach ($cards as $card) {
        // Construct the full path for the card based on the type
        $cardPath = $basePaths[$type] . $card;

        // Check if the file exists before including it
        if (file_exists($cardPath)) {
            include $cardPath;  // Include the card file
        } else {
            echo "Error: Card file $card not found in $type path.";
        }
    }
    echo '</div>';
}
?>