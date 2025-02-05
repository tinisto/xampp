<?php
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/session_util.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
        exit();
    }

    $imageKey = $data['image'];

    // Determine the image directory based on the form type
    $imageDirectory = $_SERVER["DOCUMENT_ROOT"] . "/news-images/";

    // Check if the session variable for temporary images is set
    if (isset($_SESSION['temporary_images'][$imageKey])) {
        $imagePath = $imageDirectory . $_SESSION['temporary_images'][$imageKey];
        if (file_exists($imagePath)) {
            if (unlink($imagePath)) {
                unset($_SESSION['temporary_images'][$imageKey]);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete the image file']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Image file not found']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Image not found in session']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
