<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";
include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getUserEmailById.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form data
    $schoolId = filter_input(INPUT_POST, 'id_school', FILTER_SANITIZE_NUMBER_INT);
    $approved = filter_input(INPUT_POST, 'approved', FILTER_VALIDATE_INT);
    $schoolName = filter_input(INPUT_POST, 'school_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $fullName = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $shortName = filter_input(INPUT_POST, 'short_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $site = filter_input(INPUT_POST, 'site', FILTER_SANITIZE_URL);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $fax = filter_input(INPUT_POST, 'fax', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $directorRole = filter_input(INPUT_POST, 'director_role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $directorName = filter_input(INPUT_POST, 'director_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $directorInfo = filter_input(INPUT_POST, 'director_info', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $directorPhone = filter_input(INPUT_POST, 'director_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $directorEmail = filter_input(INPUT_POST, 'director_email', FILTER_VALIDATE_EMAIL);
    $history = filter_input(INPUT_POST, 'history', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    // Retrieve the user's email
    $emailUser = getUserEmailById($connection, $userId);

    if (!$schoolId || !$schoolName || !$fullName || !$shortName || !$userId) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Prepare the SQL query
    $query = $connection->prepare("UPDATE schools SET
                                      school_name = ?,
                                      approved = ?,
                                      full_name = ?,
                                      short_name = ?,
                                      site = ?,
                                      email = ?,
                                      tel = ?,
                                      fax = ?,
                                      director_role = ?,
                                      director_name = ?,
                                      director_info = ?,
                                      director_phone = ?,
                                      director_email = ?,
                                      history = ?
                                    WHERE id_school = ?");

    if (!$query) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Bind parameters
    $query->bind_param(
        "sissssssssssssi",
        $schoolName,
        $approved,
        $fullName,
        $shortName,
        $site,
        $email,
        $tel,
        $fax,
        $directorRole,
        $directorName,
        $directorInfo,
        $directorPhone,
        $directorEmail,
        $history,
        $schoolId
    );

    // Execute the query
    if ($query->execute()) {
        if ($approved === 1) {
            // Send notifications
            include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/school-email-template-approve.php';
            sendToAdmin($subject, $body);
            sendToUser($emailUser, $subject, $body);

            // Redirect to the school page
            header("Location: /school/$schoolId");
            exit();
        } else {
            // Redirect to admin approval page
            header("Location: /dashboard/admin-approve-school.php");
            exit();
        }
    } else {
        // Handle query error
        error_log("Error updating school: " . $query->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Close the statement
    $query->close();
} else {
    // Handle invalid request method
    header("Location: /error");
    exit();
}
