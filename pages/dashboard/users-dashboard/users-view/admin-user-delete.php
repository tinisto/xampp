<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";

if (isset($_GET["id"])) {
    $userId = $_GET["id"];

    // Check if the user exists and fetch avatar filename
    $checkUserQuery = "SELECT id, avatar FROM users WHERE id = ?";
    $stmtCheckUser = $connection->prepare($checkUserQuery);

    if (!$stmtCheckUser) {
        $_SESSION["error-message"] = "Error preparing statement: " . $connection->error;
        header("Location: /pages/dashboard/users-dashboard/users-view/users-view.php");
        exit();
    }

    $stmtCheckUser->bind_param("i", $userId);
    $stmtCheckUser->execute();
    $stmtCheckUser->store_result();

    // If the user exists, proceed with deletion
    if ($stmtCheckUser->num_rows > 0) {
        // Fetch the user's avatar filename
        $stmtCheckUser->bind_result($dbUserId, $avatarFileName);
        $stmtCheckUser->fetch();

        // Delete the user's comments
        $deleteCommentsQuery = "DELETE FROM comments WHERE user_id = ?";
        $stmtDeleteComments = $connection->prepare($deleteCommentsQuery);

        if (!$stmtDeleteComments) {
            $_SESSION["error-message"] = "Error preparing statement: " . $connection->error;
            header("Location: /pages/dashboard/users-dashboard/users-view/users-view.php");
            exit();
        }

        $stmtDeleteComments->bind_param("i", $userId);

        if (!$stmtDeleteComments->execute()) {
            $_SESSION["error-message"] = "Error executing statement: " . $stmtDeleteComments->error;
            header("Location: /pages/dashboard/users-dashboard/users-view/users-view.php");
            exit();
        }

        // If the user has an avatar, delete it
        if (!empty($avatarFileName)) {
            $avatarPath =
                $_SERVER["DOCUMENT_ROOT"] .
                "/images/avatars/" .
                $avatarFileName;

            // Check if the avatar file exists and delete it
            if (file_exists($avatarPath) && is_file($avatarPath)) {
                if (!unlink($avatarPath)) {
                    $_SESSION["error-message"] = "Error deleting avatar file.";
                    header("Location: /pages/dashboard/users-dashboard/users-view/users-view.php");
                    exit();
                }
            }
        }

        // Delete the user
        $deleteUserQuery = "DELETE FROM users WHERE id = ?";
        $stmtDeleteUser = $connection->prepare($deleteUserQuery);

        if (!$stmtDeleteUser) {
            $_SESSION["error-message"] = "Error preparing statement: " . $connection->error;
            header("Location: /pages/dashboard/users-dashboard/users-view/users-view.php");
            exit();
        }

        $stmtDeleteUser->bind_param("i", $userId);

        if (!$stmtDeleteUser->execute()) {
            $_SESSION["error-message"] = "Error executing statement: " . $stmtDeleteUser->error;
            header("Location: /pages/dashboard/users-dashboard/users-view/users-view.php");
            exit();
        }

        // Success message
        $_SESSION["success-message"] = "User successfully deleted!";
        header("Location: /pages/dashboard/users-dashboard/users-view/users-view.php");
        exit();
    } else {
        // Handle the case where the user does not exist
        $_SESSION["error-message"] = "User not found.";
        header("Location: /pages/dashboard/users-dashboard/users-view/users-view.php");
        exit();
    }
}
