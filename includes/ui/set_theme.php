<?php
/**
 * Theme Setting Endpoint
 * Handles AJAX requests to change theme
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ui/dark_mode_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['theme'])) {
        $success = DarkModeManager::setTheme($input['theme']);
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Theme not provided']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Only POST method allowed']);
}
?>