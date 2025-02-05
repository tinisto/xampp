<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

$userId = $_SESSION['user_id'];
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getRussianDate.php';

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $connection->prepare($sql);
if (!$stmt) {
    header("Location: /error");
    exit();
}
$stmt->bind_param('i', $userId);
if (!$stmt->execute()) {
    header("Location: /error");
    exit();
}
$userData = $stmt->get_result()->fetch_assoc();
?>

<div class="container mt-5" style="font-size: 14px;">
    <?php
    include 'profile-intro.php';

    if ($occupation === "Представитель ВУЗа" || $occupation === "Представитель ССУЗа" || $occupation === "Представитель школы") {
        include 'profile-representative-page.php';
    }

    include 'profile-user-edit.php';
    include 'profile-user-comment.php';
    include 'profile-user-news.php';
    include 'profile-logout.php';
    ?>
</div>
