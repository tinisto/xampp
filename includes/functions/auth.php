<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/session_util.php";

if (!function_exists('ensureUserAuthenticated')) {
    /**
     * Ensures that the user is authenticated by checking if the 'email' session variable is set.
     * If not authenticated, redirects to the login page.
     */
    function ensureUserAuthenticated()
    {
        if (!isset($_SESSION['email'])) {
            header("Location: /login");
            exit();
        }
    }
}

if (!function_exists('ensureAdminAuthenticated')) {
    /**
     * Ensures that the user is authenticated and has the admin role.
     * If not authenticated or not an admin, redirects to the specified page.
     *
     * @param string $redirectPage The page to redirect to if the user is not authenticated or not an admin.
     */
    function ensureAdminAuthenticated($redirectPage = '/index.php')
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        // Check if the user is authenticated
        if (!isset($_SESSION['email'])) {
            // If not authenticated, redirect to the login page
            header("Location: $redirectPage");
            exit();
        }

        // Check if the user has the admin role
        if ($_SESSION['role'] !== 'admin') {
            // If not the admin role, redirect to the login page or another page
            header("Location: $redirectPage");
            exit();
        }
    }
}
