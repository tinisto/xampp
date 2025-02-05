<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";
include $_SERVER["DOCUMENT_ROOT"] . "/includes/url_helper.php";

// Check if the user has the required occupation
if ($_SESSION['occupation'] !== 'Представитель ВУЗа') {
    // Redirect the user if their occupation does not match
    header("Location: /unauthorized"); // Or any other page you'd like to redirect them to
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $newUniversityName = $_POST['vpo_name'];
    // Sanitize the name to remove unwanted characters
    $newUniversityName = preg_replace('/[^a-zA-Z0-9\s-]/', '', $newUniversityName);  // Remove non-alphanumeric characters except space and hyphen

    // Prepare the SQL query to prevent SQL injection
    $query = $connection->prepare("SELECT COUNT(*) AS count FROM vpo WHERE vpo_name = ?");

    // Check if the prepare() method was successful
    if ($query === false) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Bind the parameter (the 's' indicates it's a string)
    $query->bind_param("s", $newUniversityName);

    // Execute the query
    if ($query->execute()) {
        // Get the result of the query
        $queryResult = $query->get_result(); // Renamed to queryResult to avoid confusion

        if ($queryResult) {
            $row = $queryResult->fetch_assoc();
            if ($row['count'] > 0) {
                // College name already exists
                $_SESSION['form_data'] = $_POST;
                $_SESSION["error-message"] = "Это название учебного заведения уже существует. Пожалуйста, выберите другое или добавьте местоположение.";
                header("Location: /vpo-create-form.php");
                exit();
            }
        } else {
            // Handle get_result() failure
            header("Location: /error");
            exit();
        }
    } else {
        // Handle query execution failure
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Close the query after usage
    $query->close();

    // Retrieve other data from the form
    $approved = 0;
    $vpo_name = $_POST["vpo_name"];
    $full_name = $_POST["full_name"];
    $short_name = $_POST["short_name"];
    $name_rod = $_POST["name_rod"];
    $old_name = $_POST["old_name"];
    $site = $_POST["site"];
    $email = $_POST["email"];
    $tel = $_POST["tel"];
    $fax = $_POST["fax"];
    $site_pk = $_POST["site_pk"];
    $email_pk = $_POST["email_pk"];
    $tel_pk = $_POST["tel_pk"];
    $otvetcek = $_POST["otvetcek"];
    $accreditation = $_POST["accreditation"];
    $licence = $_POST["licence"];
    $director_role = $_POST["director_role"];
    $director_name = $_POST["director_name"];
    $director_info = $_POST["director_info"];
    $director_phone = $_POST["director_phone"];
    $director_email = $_POST["director_email"];
    $history = $_POST["history"];
    $vpo_url = makeUrlFriendly($vpo_name);
    $user_id = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];

    // Prepare the insert statement
    $query = $connection->prepare("INSERT INTO universities (
        approved, vpo_name, full_name, short_name, name_rod, old_name, site, email, tel, fax, site_pk, email_pk, tel_pk, otvetcek, accreditation, licence, director_role, director_name, director_info, director_phone, director_email, history, vpo_url, user_id, user_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$query) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $query->bind_param(
        "isssssssssssssssssssssi",
        $approved,
        $vpo_name,
        $full_name,
        $short_name,
        $name_rod,
        $old_name,
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
        $vpo_url,
        $user_id,
        $user_id
    );

    // Execute the insert
    $insertResult = $query->execute(); // Renamed to insertResult

    // Logic after form submission or some action
    if ($insertResult) {
        unset($_SESSION['form_data'], $_SESSION['error-message'], $_SESSION['success-message']);
        $bodyToAdmin = "vpo_name: $vpo_name";
        sendToAdmin("VPO created", $bodyToAdmin); // No need to pass adminEmail, it'll use the default
        header("Location: thank-you?message=Мы скоро с вами свяжемся. Ваша информация будет рассмотрена администратором.");
        exit();
    } else {
        $_SESSION["error-message"] = "Ошибка: " . $connection->error;
        header("Location: /index.php");
        exit();
    }

    // Close the query after execution
    $query->close();
} else {
    // If the form is not submitted, handle accordingly (you can add a message or log)
    $_SESSION["error-message"] = "Форма не отправлена.";
    header("Location: /index.php");
    exit();
}
