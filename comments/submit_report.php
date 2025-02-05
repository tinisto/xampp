<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the form
    $commentId = $_POST['commentId'];
    $userId = $_POST['userId']; // Assuming you have the user ID
    $idSchool = $_POST['idSchool']; // Assuming you have the school ID
    $reportReason = $_POST['reportReason'];
    $reportDescription = $_POST['reportDescription'];

    // Validate and sanitize the data as needed

    // Insert data into the comment_reports table
    $query = "INSERT INTO comment_reports (comment_id, user_id, id_school, report_reason, report_description) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    mysqli_stmt_bind_param($stmt, "iiiss", $commentId, $userId, $idSchool, $reportReason, $reportDescription);
    mysqli_stmt_execute($stmt);

    if ($stmt->errno) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Close the statement
    mysqli_stmt_close($stmt);

    // Provide a confirmation message
    echo 'Жалоба успешно отправлена! Спасибо за ваш отзыв.';
} else {
    // Redirect or handle the case where the form wasn't submitted properly
    header("Location: /index.php"); // Update the location as needed
    exit();
}
