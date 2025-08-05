<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/getUserEmailById.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize data from the form
    $newsId = filter_var($_POST["newsId"], FILTER_SANITIZE_NUMBER_INT);
    $categoryNews = filter_var($_POST["category_news"], FILTER_SANITIZE_NUMBER_INT);
    $approved = isset($_POST["approveNEWS"]) ? (int)$_POST["approveNEWS"] : 2;  // Default to Pending (2) if none is selected
    $newsTitle = htmlspecialchars($_POST["title_news"]);
    $descriptionNews = htmlspecialchars($_POST["description_news"]);
    $textNews = htmlspecialchars($_POST["text_news"]);
    $urlNews = filter_var($_POST["url_news"], FILTER_SANITIZE_URL);
    $userId = filter_var($_POST["user_id"], FILTER_SANITIZE_NUMBER_INT);

    $emailUser = getUserEmailById($connection, $userId);

    // Validate the inputs
    if (empty($newsId) || empty($newsTitle) || empty($descriptionNews) || empty($textNews) || empty($userId)) {
        $_SESSION["error-message"] = "All fields are required.";
        header("Location: /dashboard/news-approve.php");
        exit();
    }

    // Prepare the update statement
    $query = $connection->prepare("UPDATE news SET
                                      category_news = ?,
                                      title_news = ?,
                                      approved = ?,
                                      description_news = ?,
                                      text_news = ?,
                                      url_slug = ?,
                                      user_id = ?
                                    WHERE id_news = ?");

    // Bind parameters
    $query->bind_param(
        "isissssi",
        $categoryNews,
        $newsTitle,
        $approved,
        $descriptionNews,
        $textNews,
        $urlNews,
        $userId,
        $newsId
    );

    // Execute the update
    $result = $query->execute();

    // Check if the update was successful
    if ($result) {
        // Redirect based on approval status
        if ($approved == 1) {
            // Approved
            include $_SERVER["DOCUMENT_ROOT"] . "/includes/email-templates/news-email-template-approve.php";
            sendToAdmin($subject, $body);
            sendToUser($emailUser, $subject, $body);
            $_SESSION["success-message"] = 'Новость успешно обновлена.';
            header("Location: /news/$urlNews");
            exit();
        } elseif ($approved == 2) {
            // Pending
            $_SESSION["success-message"] = 'News successfully updated and marked as pending.';
            header("Location: /dashboard/news-approve.php");
            exit();
        } else {
            // Not Approved
            include $_SERVER["DOCUMENT_ROOT"] . "/includes/email-templates/email-template-update-request-refuse.php";
            sendToAdmin($subject, $body);
            sendToUser($emailUser, $subject, $body);
            $_SESSION["success-message"] = 'News successfully updated but not approved.';
            header("Location: /dashboard/news-approve.php");
            exit();
        }
    } else {
        $_SESSION["error-message"] = "Ошибка при обновлении новости: " . $query->error;
        header("Location: /dashboard/news-approve.php");
        exit();
    }

    // Close the statement
    $query->close();
} else {
    // If the form is not submitted
    $_SESSION["error-message"] = "Форма не отправлена.";
    header("Location: /dashboard/news-approve.php");
    exit();
}
