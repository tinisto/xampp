<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['id_post'];
    $category = $_POST['category'];
    $title = $_POST['title_post'];
    $metaDPost = $_POST['meta_d_post'];
    $metaKPost = $_POST['meta_k_post'];
    $descriptionPost = $_POST['description_post'];
    $bioPost = $_POST['bio_post'];
    $textPost = $_POST['text_post'];
    $urlPost = $_POST['url_post'];
    $viewPost = $_POST['view_post'];

    $imageUpdated = false; // Flag to track if the image was updated

    // Update general post fields
    $query = $connection->prepare("UPDATE posts SET 
                                      category = ?,
                                      title_post = ?,
                                      meta_d_post = ?,
                                      meta_k_post = ?,
                                      description_post = ?,
                                      bio_post = ?,
                                      text_post = ?,
                                      url_post = ?,
                                      view_post = ?
                                    WHERE id_post = ?");
    $query->bind_param(
        "sssssssssi",
        $category,
        $title,
        $metaDPost,
        $metaKPost,
        $descriptionPost,
        $bioPost,
        $textPost,
        $urlPost,
        $viewPost,
        $postId
    );

    if ($query->execute()) {
        // Check if the delete image checkbox is checked
        if (isset($_POST['delete_image']) && $_POST['delete_image'] === 'on') {
            // Delete the image from the server
            $currentImageQuery = "SELECT image_file_1 FROM posts WHERE id_post = ?";
            $currentImageStatement = $connection->prepare($currentImageQuery);
            $currentImageStatement->bind_param("i", $postId);
            $currentImageStatement->execute();
            $currentImageResult = $currentImageStatement->get_result();
            $currentImage = $currentImageResult->fetch_assoc();

            if ($currentImage && !empty($currentImage['image_file_1'])) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/" . $currentImage['image_file_1'];
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Delete the file from the server
                }
                // Update the database to remove the image reference
                $updateImageQuery = "UPDATE posts SET image_file_1 = NULL WHERE id_post = ?";
                $updateImageStatement = $connection->prepare($updateImageQuery);
                $updateImageStatement->bind_param("i", $postId);
                $updateImageStatement->execute();
                $updateImageStatement->close();
                echo "Image deleted successfully!";
            }
        }

        // Check if an image was uploaded
        if (isset($_FILES['image_post']) && $_FILES['image_post']['error'] === UPLOAD_ERR_OK) {
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/";

            // Check if the file is a valid image
            $check = getimagesize($_FILES['image_post']['tmp_name']);
            if ($check !== false) {
                // Generate a new filename
                $newFileName = $postId . "_1.jpg"; // Example: Using post ID
                $imageFile = $uploadDirectory . $newFileName;

                // Move the uploaded file
                if (move_uploaded_file($_FILES['image_post']['tmp_name'], $imageFile)) {
                    // Update the database with the new image filename
                    $updateImageQuery = "UPDATE posts SET image_file_1 = ? WHERE id_post = ?";
                    $updateImageStatement = $connection->prepare($updateImageQuery);
                    $updateImageStatement->bind_param("si", $newFileName, $postId);

                    if ($updateImageStatement->execute()) {
                        $imageUpdated = true;
                        echo "Image updated successfully!";
                    } else {
                        echo "Error updating image in the database: " . $updateImageStatement->error;
                    }

                    $updateImageStatement->close();
                } else {
                    echo "Error moving uploaded file.";
                }
            } else {
                echo "File is not a valid image.";
            }
        } else {
            echo "No valid file uploaded.";
        }

        // Set success message based on the image update status
        if ($imageUpdated) {
            $_SESSION["success-message"] = "Post updated successfully with image!";
        } else {
            $_SESSION["success-message"] = "Post updated successfully without image!";
        }

        // Redirect after success
        header("Location: /post/$urlPost");
        exit();
    } else {
        $_SESSION["error-message"] = "Error updating post: " . $query->error;
    }

    $query->close();
} else {
    echo "Форма не отправлена.";
}