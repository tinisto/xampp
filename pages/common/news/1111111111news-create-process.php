<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/auth.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/generateSlug.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/makeUrlFriendly.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/generateUniqueSlugForNews.php";

// Define constants
define("UPLOAD_DIR", $_SERVER["DOCUMENT_ROOT"] . "/images/news-images/");

// Check if user has a valid role (admin or user)
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get current date for user/admin-specific date field
$currentDate = date("Y-m-d");

// Sanitize form inputs
$category = filter_input(INPUT_POST, "category_news", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$title = filter_input(INPUT_POST, "title_news", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$metaDesc = filter_input(INPUT_POST, "meta_d_news", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$metaKey = filter_input(INPUT_POST, "meta_k_news", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$description = filter_input(INPUT_POST, "description_news", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$text = filter_input(INPUT_POST, "text_news", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$date = filter_input(INPUT_POST, "date_news", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';  // User ID for hidden input

// Generate a unique URL slug for the news item
$url = generateUniqueSlugForNews($title, $connection);

// Insert news into the database (common function)
function insertNews($connection, $userId, $category, $title, $metaDesc, $metaKey, $description, $text, $url, $date, $isAdmin, $imageNews1 = '', $imageNews2 = '', $imageNews3 = '')
{
    $metaDesc = !empty($metaDesc) ? $metaDesc : '';
    $metaKey = !empty($metaKey) ? $metaKey : '';
    $approved = $isAdmin ? 1 : 2; // Set approved status based on user type (admin or user)

    $query = "INSERT INTO news (user_id, category_news, title_news, meta_d_news, meta_k_news, description_news, text_news, url_news, date_news, approved, image_news_1, image_news_2, image_news_3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("issssssssisss", $userId, $category, $title, $metaDesc, $metaKey, $description, $text, $url, $date, $approved, $imageNews1, $imageNews2, $imageNews3);

    if ($stmt->execute()) {
        return $connection->insert_id;
    } else {
        error_log("Database error: " . $stmt->error);
        return ["error" => $stmt->error];
    }
}

// Handle image upload (common function)
function handleImageUpload($file, $uploadDirectory, $newsId, $imageIndex)
{
    $check = getimagesize($file["tmp_name"]);
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if ($check === false || !in_array($fileExtension, $allowedExtensions)) {
        return ["error" => "Invalid image file type. Only JPG, PNG, and GIF are allowed."];
    }

    $newFileName = $newsId . "_" . $imageIndex . "." . $fileExtension;
    $imageFilePath = $uploadDirectory . $newFileName;

    if (move_uploaded_file($file["tmp_name"], $imageFilePath)) {
        return ["success" => $newFileName];
    } else {
        error_log("Error uploading the image: " . $file["error"]);
        return ["error" => "Error uploading the image."];
    }
}

// Main processing logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Initialize image file names
    $imageNews1 = '';
    $imageNews2 = '';
    $imageNews3 = '';

    // Insert news into database
    $newsId = insertNews($connection, $userId, $category, $title, $metaDesc, $metaKey, $description, $text, $url, $date, $isAdmin, $imageNews1, $imageNews2, $imageNews3);

    if (is_array($newsId) && isset($newsId["error"])) {
        $_SESSION["error-message"] = "Database error: " . $newsId["error"];
        header("Location: /pages/news/news-create.php");
        exit();
    }

    // Handle image uploads if they exist
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_FILES["image_news_$i"]) && $_FILES["image_news_$i"]["error"] !== UPLOAD_ERR_NO_FILE) {
            $imageResult = handleImageUpload($_FILES["image_news_$i"], UPLOAD_DIR, $newsId, $i);

            if (isset($imageResult["error"])) {
                $_SESSION["error-message"] = $imageResult["error"];
                header("Location: /pages/news/news-create.php");
                exit();
            }

            // Update the news with the image name
            $updateQuery = "UPDATE news SET image_news_$i = ? WHERE id_news = ?";
            $updateStmt = $connection->prepare($updateQuery);
            $updateStmt->bind_param("si", $imageResult["success"], $newsId);

            if (!$updateStmt->execute()) {
                $_SESSION["error-message"] = "Error updating image in database: " . $updateStmt->error;
                header("Location: /pages/news/news-create.php");
                exit();
            }
        }
    }

    if ($isAdmin) {
        header("Location: /news/$url");
        exit();
    }

    $_SESSION["message"] = "Новость успешно создана и отправлена на модерацию. Мы рассмотрим её в ближайшее время.";
    header("Location: /thank-you");
    exit();
} else {
    echo "Invalid request method.";
}
