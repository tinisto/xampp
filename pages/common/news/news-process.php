<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/auth.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/generateSlug.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/makeUrlFriendly.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/generateUniqueSlugForNews.php";

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define constants
define("UPLOAD_DIR", $_SERVER["DOCUMENT_ROOT"] . "/images/news-images/");

// Check if user has a valid role (admin or user)
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get current date for user/admin-specific date field
$currentDate = date("Y-m-d");

// Sanitize form inputs
$category = filter_input(INPUT_POST, "category_news", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$title = htmlspecialchars($_POST["title_news"] ?? '', ENT_QUOTES, 'UTF-8');
$metaDesc = htmlspecialchars($_POST["meta_d_news"] ?? '', ENT_QUOTES, 'UTF-8');
$metaKey = htmlspecialchars($_POST["meta_k_news"] ?? '', ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars($_POST["description_news"] ?? '', ENT_QUOTES, 'UTF-8');
$text = htmlspecialchars($_POST["text_news"] ?? '', ENT_QUOTES, 'UTF-8');
$date = filter_input(INPUT_POST, "date_news", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';  // User ID for hidden input
$newsId = filter_input(INPUT_POST, "news_id", FILTER_SANITIZE_NUMBER_INT); // Corrected to news_id

// Initialize $approved with a default value before calling saveNews
$approved = filter_input(INPUT_POST, "approved", FILTER_SANITIZE_NUMBER_INT);

// If the approved value is null or false, set it to 1 (admin) or 2 (user)
if ($approved === null || $approved === false) {
    $approved = $isAdmin ? 1 : 2;
}

// Fetch existing news data if editing
$existingImages = [];
$existingTitle = '';
$existingUrl = '';
if ($newsId) {
    $query = "SELECT image_news_1, image_news_2, image_news_3, title_news, url_news, user_id FROM news WHERE id_news = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $newsId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $existingNewsData = $result->fetch_assoc();
        $existingImages = [
            'image_news_1' => $existingNewsData['image_news_1'],
            'image_news_2' => $existingNewsData['image_news_2'],
            'image_news_3' => $existingNewsData['image_news_3']
        ];
        $existingTitle = $existingNewsData['title_news'];
        $existingUrl = $existingNewsData['url_news'];
        $originalUserId = $existingNewsData['user_id']; // Keep the original user ID
    }
    $stmt->close();
}

// Generate a unique URL slug for the news item if the title has changed
$url = $existingTitle === $title ? $existingUrl : generateUniqueSlugForNews($title, $connection);

// Insert or update news in the database
function saveNews($connection, $userId, $category, $title, $metaDesc, $metaKey, $description, $text, $url, $date, $isAdmin, $newsId, $approved, $imageNews1 = '', $imageNews2 = '', $imageNews3 = '')
{
    $metaDesc = !empty($metaDesc) ? $metaDesc : '';
    $metaKey = !empty($metaKey) ? $metaKey : '';

    if ($newsId) {
        // Update existing news
        $query = "UPDATE news SET
                    category_news = ?,
                    title_news = ?,
                    meta_d_news = ?,
                    meta_k_news = ?,
                    description_news = ?,
                    text_news = ?,
                    url_slug = ?,
                    date_news = ?,
                    approved = ?,
                    image_news_1 = ?,
                    image_news_2 = ?,
                    image_news_3 = ?,
                    date_edited = CURRENT_TIMESTAMP
                  WHERE id_news = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ssssssssisssi", $category, $title, $metaDesc, $metaKey, $description, $text, $url, $date, $approved, $imageNews1, $imageNews2, $imageNews3, $newsId);
    } else {
        // Insert new news
        $query = "INSERT INTO news (user_id, category_news, title_news, meta_d_news, meta_k_news, description_news, text_news, url_news, date_news, approved, image_news_1, image_news_2, image_news_3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("issssssssisss", $userId, $category, $title, $metaDesc, $metaKey, $description, $text, $url, $date, $approved, $imageNews1, $imageNews2, $imageNews3);
    }

    if ($stmt->execute()) {
        return $newsId ? $newsId : $connection->insert_id;
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
    $imageNews1 = $existingImages['image_news_1'] ?? '';
    $imageNews2 = $existingImages['image_news_2'] ?? '';
    $imageNews3 = $existingImages['image_news_3'] ?? '';

    // Use the original user ID if editing
    $userId = $newsId ? $originalUserId : $userId;

    // Save news into database
    $newsId = saveNews($connection, $userId, $category, $title, $metaDesc, $metaKey, $description, $text, $url, $date, $isAdmin, $newsId, $approved, $imageNews1, $imageNews2, $imageNews3);

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

    if ($isAdmin || $approved == 1) {
        // Redirection URL after successful news creation
        $redirectionUrl = "/news/$url";
        header("Location: $redirectionUrl");
        exit();
    }

    $_SESSION["message"] = "Новость успешно создана и отправлена на модерацию. Мы рассмотрим её в ближайшее время.";
    header("Location: /thank-you");
    exit();
} else {
    echo "Invalid request method.";
}
