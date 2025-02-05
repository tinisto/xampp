<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";


// Get the user's current information
$email = $_SESSION['email'];

// Handle updating personal data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updatePersonalData'])) {
    // Sanitize and validate inputs
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $occupation = trim($_POST["occupation"]);

    // Input validation (You can add more validation as needed)
    if (empty($firstname) || empty($lastname) || empty($occupation)) {
        $_SESSION['error-message'] = "Все поля обязательны для заполнения.";
        header("Location: /account");
        exit();
    }

    // Update personal data in the database
    $updatePersonalDataQuery = "UPDATE users SET firstname = ?, lastname = ?, occupation = ? WHERE email = ?";
    $stmt = $connection->prepare($updatePersonalDataQuery);

    if ($stmt === false) {
        $_SESSION['error-message'] = "Ошибка подготовки запроса. Пожалуйста, попробуйте снова.";
        header("Location: /account");
        exit();
    }

    // Bind parameters and execute query
    $stmt->bind_param("ssss", $firstname, $lastname, $occupation, $email);

    // Check if the update was successful
    if ($stmt->execute()) {
        // Update session variables for first name, last name, and occupation
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;
        $_SESSION['occupation'] = $occupation;

        // Set a session variable for success
        $_SESSION['success-message'] = "Данные обновлены успешно.";

        // Redirect back to profile page
        header("Location: /account");
        exit();
    } else {
        // Set a session variable for the error
        $_SESSION['error-message'] = "Ошибка при обновлении данных. Пожалуйста, попробуйте снова.";
        header("Location: /account");
        exit();
    }

    // Close the statement
    $stmt->close();
}

// Close the main database connection
$connection->close();
