<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/generateSlug.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $author = isset($_POST["author_post"]) ? filter_var($_POST["author_post"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "";
    $category = isset($_POST["category"]) ? filter_var($_POST["category"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "";
    $titlePost = isset($_POST["title_post"]) ? filter_var($_POST["title_post"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "";
    $metaDPost = isset($_POST["meta_d_post"]) ? filter_var($_POST["meta_d_post"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "";
    $metaKPost = isset($_POST["meta_k_post"]) ? filter_var($_POST["meta_k_post"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "";
    $descriptionPost = isset($_POST["description_post"]) ? filter_var($_POST["description_post"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "";
    $bioPost = isset($_POST["bio_post"]) ? filter_var($_POST["bio_post"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "";
    $textPost = isset($_POST["text_post"]) ? filter_var($_POST["text_post"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "";
    $datePost = isset($_POST["date_post"]) ? filter_var($_POST["date_post"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "";

    $urlPost = generateSlug($titlePost);

    // Initialize image filenames
    $imageFile1 = null;
    $imageFile2 = null;
    $imageFile3 = null;

    // Insert post data into the database
    $insertPostQuery = "INSERT INTO posts (author_post, category, title_post, meta_d_post, meta_k_post, description_post, bio_post, text_post, url_post, date_post, image_post_1, image_post_2, image_post_3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insertPostStatement = $connection->prepare($insertPostQuery);
    $insertPostStatement->bind_param("sssssssssssss", $author, $category, $titlePost, $metaDPost, $metaKPost, $descriptionPost, $bioPost, $textPost, $urlPost, $datePost, $imageFile1, $imageFile2, $imageFile3);
    $postResult = $insertPostStatement->execute();

    if ($postResult) {
        $lastPostId = mysqli_insert_id($connection);

        for ($i = 1; $i <= 3; $i++) {
            if (isset($_FILES["image_post_$i"]) && $_FILES["image_post_$i"]["error"] !== UPLOAD_ERR_NO_FILE) {
                $uploadDirectory = $_SERVER["DOCUMENT_ROOT"] . "/images/posts-images/";
                $check = getimagesize($_FILES["image_post_$i"]["tmp_name"]);

                if ($check !== false) {
                    $newFileName = $lastPostId . "_$i.jpg";
                    $imageFile = $uploadDirectory . $newFileName;

                    if (move_uploaded_file($_FILES["image_post_$i"]["tmp_name"], $imageFile)) {
                        // Update the post with the image filename
                        $updatePostQuery = "UPDATE posts SET image_post_$i = ? WHERE id_post = ?";
                        $updatePostStatement = $connection->prepare($updatePostQuery);
                        $updatePostStatement->bind_param("si", $newFileName, $lastPostId);
                        $updateResult = $updatePostStatement->execute();

                        if (!$updateResult) {
                            $_SESSION["error-message"] = "Error updating post information: " . $updatePostStatement->error;
                        }

                        $updatePostStatement->close();
                    } else {
                        $_SESSION["error-message"] = "Error uploading image.";
                    }
                } else {
                    $_SESSION["error-message"] = "File is not an image.";
                }
            }
        }

        $_SESSION["success-message"] = "Post created successfully!";
        header("Location: /post/$urlPost");
        exit();
    } else {
        $_SESSION["error-message"] = "Error saving post information: " . $insertPostStatement->error;
    }

    $insertPostStatement->close();
} else {
    echo "Форма не отправлена.";
}
