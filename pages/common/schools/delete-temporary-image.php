<?php
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/session_util.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $imageKey = $data['image'];

    if (isset($_SESSION['temporary_images'][$imageKey])) {
        $imagePath = $_SERVER["DOCUMENT_ROOT"] . "/images/schools-images/" . $_SESSION['temporary_images'][$imageKey];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        unset($_SESSION['temporary_images'][$imageKey]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Image not found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
