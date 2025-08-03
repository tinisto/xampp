<?php
require_once __DIR__ . '/../../includes/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';

$errors = [];
$oldData = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['g-recaptcha-response'])) {
    // Build POST request:
    $captcha = $_POST['g-recaptcha-response'];
    
    try {
        $recaptchaConfig = Environment::getRecaptchaConfig();
        $secret = $recaptchaConfig['secret_key'];
    } catch (Exception $e) {
        // Fallback for backward compatibility - remove this after .env is set up
        $secret = '6LcBTE4pAAAAALqF7QTwR_2cr1sAP7EuVRF3h3jq';
        error_log('reCAPTCHA secret key not found in environment variables. Using fallback.');
    }
    
    $action = "submit";

    // Call curl to POST request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $secret, 'response' => $captcha)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $arrResponse = json_decode($response, true);

    // Verify the response
    if ($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= 0.5) {
        // Retrieve form data
        $password = $_POST["password"];
        $email = trim($_POST["email"]);
        $timezone = $_POST['timezone'];
        $occupation = $_POST['occupation'];

        // Validate form fields
        if (empty($password)) {
            $errors[] = "Введите пароль.";
        }

        if (empty($occupation)) {
            $errors[] = "Выберите род деятельности.";
        }

        // Check if email is in a valid format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Введите действительный адрес электронной почты.";
        }

        // Check if email already exists
        $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
        if (isset($connection)) {
            $stmt = $connection->prepare($checkEmailQuery);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $errors[] = "Адрес электронной почты $email уже зарегистрирован.";
            }
            $stmt->close();
        }

        if (empty($errors)) {
            // Hash the password securely
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Set default role
            $defaultRole = "user";

            // Generate activation token
            $activationToken = bin2hex(random_bytes(32));

            // Insert user data into the database
            $insertQuery = "INSERT INTO users (password, email, role, activation_token, timezone, occupation) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($insertQuery);

            if (!$stmt) {
                $errors[] = "Ошибка подготовки запроса: " . $connection->error;
            } else {
                $stmt->bind_param("ssssss", $hashedPassword, $email, $defaultRole, $activationToken, $timezone, $occupation);

                if (!$stmt->execute()) {
                    $errors[] = "Ошибка выполнения запроса: " . $stmt->error;
                }

                $stmt->close();
            }

            // Generate activation link
            $activationLink = "https://$_SERVER[HTTP_HOST]/pages/registration/activate_account/activate_account.php?token=$activationToken";

            // Store activation link and token in the database
            $updateLinkQuery = "UPDATE users SET activation_link = ?, activation_token = ? WHERE email = ?";
            $stmt = $connection->prepare($updateLinkQuery);

            if (!$stmt) {
                $errors[] = "Ошибка подготовки запроса: " . $connection->error;
            } else {
                $stmt->bind_param("sss", $activationLink, $activationToken, $email);

                if (!$stmt->execute()) {
                    $errors[] = "Ошибка выполнения запроса: " . $stmt->error;
                }

                $stmt->close();
            }

            include $_SERVER["DOCUMENT_ROOT"] . "/includes/email-templates/email-template-register-initial.php";

            $subjectAdmin = "We have a new user: $email";
            $bodyAdmin = "We have a new user:$email";

            sendToUser($email, $subject, $body);
            sendToAdmin($subjectAdmin, $bodyAdmin);

            $successMessage = "Регистрация успешна! Пожалуйста, проверьте свою электронную почту для активации аккаунта.";
            header("Location: /login?registration_success=true&message=" . urlencode($successMessage));
            exit();
        }
    } else {
        $errors[] = "Проверка reCAPTCHA не удалась.";
    }
}

$connection->close();

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['oldData'] = $oldData;
    header("Location: /registration");
    exit();
}
