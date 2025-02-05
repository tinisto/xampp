<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/redirectToErrorPage.php";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve data from the form
    $schoolId = filter_input(INPUT_POST, 'id_school', FILTER_VALIDATE_INT);
    $id_rono = filter_input(INPUT_POST, 'id_rono', FILTER_VALIDATE_INT);
    $schoolName = filter_input(INPUT_POST, 'school_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $fullName = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $shortName = filter_input(INPUT_POST, 'short_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $site = filter_input(INPUT_POST, 'site', FILTER_SANITIZE_URL);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_NUMBER_INT);
    $fax = filter_input(INPUT_POST, 'fax', FILTER_SANITIZE_NUMBER_INT);
    $director_name = filter_input(INPUT_POST, 'director_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $director_role = filter_input(INPUT_POST, 'director_role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $director_info = filter_input(INPUT_POST, 'director_info', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $director_email = filter_input(INPUT_POST, 'director_email', FILTER_SANITIZE_EMAIL);
    $director_phone = filter_input(INPUT_POST, 'director_phone', FILTER_SANITIZE_NUMBER_INT);
    $history = filter_input(INPUT_POST, 'history', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
    // if (!$schoolId || !$email || !$director_email) {
    //     redirectToErrorPage($connection->error, __FILE__, __LINE__);
    // }

    // Prepare the query to insert into schools_verification table
    $verificationQuery = $connection->prepare("INSERT INTO schools_verification (
        id_school,
        id_rono,
        school_name,
        full_name,
        short_name,
        tel,
        fax,
        site,
        email,
        director_name,
        director_role,
        director_info,
        director_email,
        director_phone,
        history,
        user_id,
        view,
        zip_code,
        id_town,
        id_area,
        id_region,
        id_country,
        year,
        street
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$verificationQuery) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Bind parameters
    $verificationQuery->bind_param(
        "iissssssssssssiiiiiiss",
        $schoolId,
        $id_rono,
        $schoolName,
        $fullName,
        $shortName,
        $tel,
        $fax,
        $site,
        $email,
        $director_name,
        $director_role,
        $director_info,
        $director_email,
        $director_phone,
        $history,
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

    // Execute the query to insert into schools_verification table
    $verificationResult = $verificationQuery->execute();

    // Check if the insertion was successful
    if ($verificationResult) {
        $subject = $schoolName . " - School updated";
        $body = "School updated<br><br>
            <strong>user: </strong>$user_id<br><br>
            <strong>school_name:</strong> $schoolName<br>
            <strong>full_name:</strong> $fullName<br>
            <strong>short_name:</strong> $shortName<br>
            <strong>site:</strong> $site<br>
            <strong>email:</strong> $email<br>
            <strong>tel:</strong> $tel<br>
            <strong>fax:</strong> $fax<br>
            <strong>director_role:</strong> $director_role<br>
            <strong>director_name:</strong> $director_name<br>
            <strong>director_info:</strong> $director_info<br>
            <strong>director_phone:</strong> $director_phone<br>
            <strong>director_email:</strong> $director_email<br>
            <strong>history:</strong> $history<br>";

        sendToAdmin($subject, $body);

        // Dynamically redirect before echoing any content
        header("Location: thank-you?message=Ваш запрос успешно отправлен и находится в процессе проверки.");
        exit(); // It's crucial to exit the script after redirection
    } else {
        // Handle error if the query execution fails
        error_log("Error updating schools: " . $verificationQuery->error);  // Log the error to a file
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Close the query for the schools_verification table
    $verificationQuery->close();
} else {
    // If the form is not submitted, handle accordingly (you can add a message or log entry)
    header("Location: /error");
    exit();
}
