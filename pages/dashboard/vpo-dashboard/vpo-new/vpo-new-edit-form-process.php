<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";
include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getUserEmailById.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and validate the input data
    $id_vpo = filter_input(INPUT_POST, 'id_vpo', FILTER_SANITIZE_NUMBER_INT);
    $approved = filter_input(INPUT_POST, 'approved', FILTER_SANITIZE_NUMBER_INT);

    // Sanitize fields for HTML output
    $universityName = filter_input(INPUT_POST, 'vpo_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // For HTML display
    $nameRod = filter_input(INPUT_POST, 'name_rod', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $fullName = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $shortName = filter_input(INPUT_POST, 'short_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $oldName = filter_input(INPUT_POST, 'old_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $url = filter_input(INPUT_POST, 'vpo_url', FILTER_SANITIZE_URL); // URL sanitization
    $site = filter_input(INPUT_POST, 'site', FILTER_SANITIZE_URL); // URL sanitization
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); // Email sanitization

    // Use FILTER_SANITIZE_FULL_SPECIAL_CHARS for text-based fields (replaces FILTER_SANITIZE_STRING)
    $tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $fax = filter_input(INPUT_POST, 'fax', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $site_pk = filter_input(INPUT_POST, 'site_pk', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email_pk = filter_input(INPUT_POST, 'email_pk', FILTER_SANITIZE_EMAIL); // Email sanitization
    $tel_pk = filter_input(INPUT_POST, 'tel_pk', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $otvetcek = filter_input(INPUT_POST, 'otvetcek', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $accreditation = filter_input(INPUT_POST, 'accreditation', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $licence = filter_input(INPUT_POST, 'licence', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $director_role = filter_input(INPUT_POST, 'director_role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $director_name = filter_input(INPUT_POST, 'director_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $director_info = filter_input(INPUT_POST, 'director_info', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $director_phone = filter_input(INPUT_POST, 'director_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Phone number
    $director_email = filter_input(INPUT_POST, 'director_email', FILTER_SANITIZE_EMAIL); // Email sanitization
    $history = filter_input(INPUT_POST, 'history', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    // Get the email address of the user
    $emailUser = getUserEmailById($connection, $userId);

    // Prepare the update statement
    $query = $connection->prepare("UPDATE universities SET
                                      vpo_name = ?, approved = ?, name_rod = ?, full_name = ?,
                                      short_name = ?, old_name = ?, vpo_url = ?, site = ?, email = ?,
                                      tel = ?, fax = ?, site_pk = ?, email_pk = ?, tel_pk = ?,
                                      otvetcek = ?, accreditation = ?, licence = ?, director_role = ?,
                                      director_name = ?, director_info = ?, director_phone = ?,
                                      director_email = ?, history = ? WHERE id_vpo = ?");

    if ($query === false) {
        // Handle the error if the prepare fails
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Bind parameters
    $query->bind_param(
        "sisssssssssssssssssi",
        $universityName,
        $approved,
        $nameRod,
        $fullName,
        $shortName,
        $oldName,
        $url,
        $site,
        $email,
        $tel,
        $fax,
        $site_pk,
        $email_pk,
        $tel_pk,
        $otvetcek,
        $accreditation,
        $licence,
        $director_role,
        $director_name,
        $director_info,
        $director_phone,
        $director_email,
        $history,
        $id_vpo
    );

    // Execute the update
    $result = $query->execute();

    // Check if the update was successful
    if ($result) {
        if ($approved == 1) {
            // Include the email template and send emails
            include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/vpo-email-template-approve.php';
            sendToAdmin($subject, $body);
            sendToUser($emailUser, $subject, $body);
            header("Location: /vpo/$url");
            exit();
        } else {
            $_SESSION['error-message'] = "Not approved";
            header("Location: /dashboard/admin-approve-vpo.php");
            exit();
        }
    } else {
        // Handle error if the query execution fails
        error_log("Error updating universities: " . $query->error);  // Log the error to a file
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Close the statement
    $query->close();
} else {
    // Handle form not submitted
    header("Location: /error");
    exit();
}
