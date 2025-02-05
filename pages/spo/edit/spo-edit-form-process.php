<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and assign form data
    $collegeId = filter_input(INPUT_POST, 'id_spo', FILTER_VALIDATE_INT);
    $parent_spo_id = filter_input(INPUT_POST, 'parent_spo_id', FILTER_VALIDATE_INT);
    $filials_spo = filter_input(INPUT_POST, 'filials_spo', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $directorName = filter_input(INPUT_POST, 'director_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $directorRole = filter_input(INPUT_POST, 'director_role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $directorInfo = filter_input(INPUT_POST, 'director_info', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $collegeName = filter_input(INPUT_POST, 'spo_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $nameRod = filter_input(INPUT_POST, 'name_rod', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $fullName = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $shortName = filter_input(INPUT_POST, 'short_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $oldName = filter_input(INPUT_POST, 'old_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_NUMBER_INT); // Use NUMBER_INT for phone numbers
    $fax = filter_input(INPUT_POST, 'fax', FILTER_SANITIZE_NUMBER_INT); // Use NUMBER_INT for fax numbers
    $accreditation = filter_input(INPUT_POST, 'accreditation', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $licence = filter_input(INPUT_POST, 'licence', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $site = filter_input(INPUT_POST, 'site', FILTER_SANITIZE_URL);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email_pk = filter_input(INPUT_POST, 'email_pk', FILTER_SANITIZE_EMAIL);
    $tel_pk = filter_input(INPUT_POST, 'tel_pk', FILTER_SANITIZE_NUMBER_INT); // Use NUMBER_INT for phone numbers
    $otvetcek = filter_input(INPUT_POST, 'otvetcek', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $site_pk = filter_input(INPUT_POST, 'site_pk', FILTER_SANITIZE_URL);
    $directorEmail = filter_input(INPUT_POST, 'director_email', FILTER_SANITIZE_EMAIL);
    $directorPhone = filter_input(INPUT_POST, 'director_phone', FILTER_SANITIZE_NUMBER_INT); // Use NUMBER_INT for phone numbers
    $history = filter_input(INPUT_POST, 'history', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $collegeUrl = filter_input(INPUT_POST, 'spo_url', FILTER_SANITIZE_URL);
    $user_id = $_SESSION['email']; // Get the user email from the session
    $view = filter_input(INPUT_POST, 'view', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $zip_code = filter_input(INPUT_POST, 'zip_code', FILTER_VALIDATE_INT);
    $id_town = filter_input(INPUT_POST, 'id_town', FILTER_VALIDATE_INT);
    $id_area = filter_input(INPUT_POST, 'id_area', FILTER_VALIDATE_INT);
    $id_region = filter_input(INPUT_POST, 'id_region', FILTER_VALIDATE_INT);
    $id_country = filter_input(INPUT_POST, 'id_country', FILTER_VALIDATE_INT);
    $year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);
    $street = filter_input(INPUT_POST, 'street', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate necessary fields
    if (!$collegeId || !$email || !$directorEmail) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Prepare the query to insert into spo_verification table
    $query = "
        INSERT INTO spo_verification (
            id_spo, parent_spo_id, filials_spo, director_name, director_role, director_info,
            spo_name, name_rod, full_name, short_name, old_name, tel, fax, accreditation, licence, site,
            email, email_pk, tel_pk, otvetcek, site_pk, director_email, director_phone, history, spo_url,
            user_id, view, zip_code, id_town, id_area, id_region, id_country, year, street
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and execute the query with parameter binding
    if ($stmt = $connection->prepare($query)) {
        $stmt->bind_param(
            "iissssssssssssssssssssiiiiiiss",
            $collegeId,
            $parent_spo_id,
            $filials_spo,
            $directorName,
            $directorRole,
            $directorInfo,
            $collegeName,
            $nameRod,
            $fullName,
            $shortName,
            $oldName,
            $tel,
            $fax,
            $accreditation,
            $licence,
            $site,
            $email,
            $email_pk,
            $tel_pk,
            $otvetcek,
            $site_pk,
            $directorEmail,
            $directorPhone,
            $history,
            $collegeUrl,
            $user_id,
            $view,
            $zip_code,
            $id_town,
            $id_area,
            $id_region,
            $id_country,
            $year,
            $street
        );

        // Execute the query
        if ($stmt->execute()) {
            // Prepare email body for notification
            $body = "SPO updated<br><br>
                    <strong>user: </strong>$user_id<br><br>
                    <strong>spo_name:</strong> $collegeName<br>
                    <strong>name_rod:</strong> $nameRod<br>
                    <strong>full_name:</strong> $fullName<br>
                    <strong>short_name:</strong> $shortName<br>
                    <strong>old_name:</strong> $oldName<br>
                    <strong>spo_url:</strong> $collegeUrl<br>
                    <strong>site:</strong> $site<br>
                    <strong>email:</strong> $email<br>
                    <strong>tel:</strong> $tel<br>
                    <strong>fax:</strong> $fax<br>
                    <strong>site_pk:</strong> $site_pk<br>
                    <strong>email_pk:</strong> $email_pk<br>
                    <strong>tel_pk:</strong> $tel_pk<br>
                    <strong>otvetcek:</strong> $otvetcek<br>
                    <strong>accreditation:</strong> $accreditation<br>
                    <strong>licence:</strong> $licence<br>
                    <strong>director_role:</strong> $directorRole<br>
                    <strong>director_name:</strong> $directorName<br>
                    <strong>director_info:</strong> $directorInfo<br>
                    <strong>director_phone:</strong> $directorPhone<br>
                    <strong>director_email:</strong> $directorEmail<br>
                    <strong>history:</strong> $history<br>";

            $subject = $collegeName . " - SPO updated";
            sendToAdmin($subject, $body);

            // Redirect with success message
            header("Location: thank-you?message=Ваш запрос успешно отправлен и находится в процессе проверки.");
            exit(); // Ensure script exits after redirect
        } else {
            header("Location: /error");
            exit();
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }
} else {
    header("Location: /error");
    exit();
}
