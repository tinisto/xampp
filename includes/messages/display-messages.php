<?php
// Check and display success message
if (isset($_SESSION["success-message"])) {
    echo '<div class="alert alert-success">' .
        htmlspecialchars($_SESSION["success-message"], ENT_QUOTES, "UTF-8") .
        "</div>";
    unset($_SESSION["success-message"]); // Clear the message after displaying
}

// Check and display error message
if (isset($_SESSION["error-message"])) {
    echo '<div class="alert alert-danger">' .
        htmlspecialchars($_SESSION["error-message"], ENT_QUOTES, "UTF-8") .
        "</div>";
    unset($_SESSION["error-message"]); // Clear the message after displaying
}

// Check and display other (warning) message
if (isset($_SESSION["warning-message"])) {
    echo '<div class="alert alert-warning">' .
        htmlspecialchars($_SESSION["warning-message"], ENT_QUOTES, "UTF-8") .
        "</div>";
    unset($_SESSION["warning-message"]); // Clear the message after displaying
}
?>
