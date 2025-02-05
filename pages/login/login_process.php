<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';


// Check if email and password are set
if (isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statement to avoid SQL injection
    $stmt = $connection->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];
        $role = $row['role'];
        $is_active = $row['is_active'];

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            if ($is_active) {
                // Set up a session if the account is active
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                $_SESSION['firstname'] = $row['firstname'];
                $_SESSION['lastname'] = $row['lastname'];
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['avatar'] = $row['avatar'];
                $_SESSION['occupation'] = $row['occupation'];

                // Redirect based on role
                if ($role === 'admin') {
                    header('Location: /dashboard');
                } else {
                    header('Location: /index.php');
                }
                exit();
            } else {
                header('Location: /pages/registration/resend_activation/resend_activation.php?email=' . urlencode($email));
                exit();
                // Redirect to the resend activation page

            }
        } else {
            header('Location: /login?error=1');
            exit();
            // Authentication failed

        }
    }

    // Close the statement and connection
    $stmt->close();
    $connection->close();
}

header('Location: /login?error=1');
exit();
// Redirect if email and password are not set or if user authentication fails
