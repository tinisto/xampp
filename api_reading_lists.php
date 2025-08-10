<?php
// Reading lists API endpoint
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Необходима авторизация']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'add_to_list' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add item to reading list
    $input = json_decode(file_get_contents('php://input'), true);
    
    $listId = intval($input['list_id'] ?? 0);
    $itemType = $input['item_type'] ?? '';
    $itemId = intval($input['item_id'] ?? 0);
    
    // Validate input
    if (!in_array($itemType, ['news', 'post', 'vpo', 'spo', 'school'])) {
        echo json_encode(['error' => 'Неверный тип материала']);
        exit;
    }
    
    if ($itemId <= 0 || $listId <= 0) {
        echo json_encode(['error' => 'Неверные параметры']);
        exit;
    }
    
    // Check if user owns the list
    $list = db_fetch_one("
        SELECT id FROM reading_lists 
        WHERE id = ? AND user_id = ?
    ", [$listId, $_SESSION['user_id']]);
    
    if (!$list) {
        echo json_encode(['error' => 'Список не найден']);
        exit;
    }
    
    // Check if item already exists in list
    $existing = db_fetch_one("
        SELECT id FROM reading_list_items 
        WHERE list_id = ? AND item_type = ? AND item_id = ?
    ", [$listId, $itemType, $itemId]);
    
    if ($existing) {
        echo json_encode(['error' => 'Материал уже добавлен в этот список']);
        exit;
    }
    
    // Add item to list
    $insertId = db_insert_id("
        INSERT INTO reading_list_items (list_id, item_type, item_id)
        VALUES (?, ?, ?)
    ", [$listId, $itemType, $itemId]);
    
    if ($insertId) {
        echo json_encode(['success' => true, 'message' => 'Материал добавлен в список']);
    } else {
        echo json_encode(['error' => 'Ошибка при добавлении в список']);
    }
    
} elseif ($action === 'get_user_lists' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get user's reading lists for dropdown
    $lists = db_fetch_all("
        SELECT id, name, 
               (SELECT COUNT(*) FROM reading_list_items WHERE list_id = reading_lists.id) as item_count
        FROM reading_lists 
        WHERE user_id = ?
        ORDER BY name
    ", [$_SESSION['user_id']]);
    
    echo json_encode(['success' => true, 'lists' => $lists]);
    
} elseif ($action === 'quick_add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Quick add to "Read Later" list (create if doesn't exist)
    $input = json_decode(file_get_contents('php://input'), true);
    
    $itemType = $input['item_type'] ?? '';
    $itemId = intval($input['item_id'] ?? 0);
    
    // Validate input
    if (!in_array($itemType, ['news', 'post', 'vpo', 'spo', 'school'])) {
        echo json_encode(['error' => 'Неверный тип материала']);
        exit;
    }
    
    if ($itemId <= 0) {
        echo json_encode(['error' => 'Неверный ID материала']);
        exit;
    }
    
    // Get or create "Read Later" list
    $readLaterList = db_fetch_one("
        SELECT id FROM reading_lists 
        WHERE user_id = ? AND name = 'Читать позже'
        LIMIT 1
    ", [$_SESSION['user_id']]);
    
    if (!$readLaterList) {
        // Create "Read Later" list
        $listId = db_insert_id("
            INSERT INTO reading_lists (user_id, name, description)
            VALUES (?, 'Читать позже', 'Материалы для изучения позже')
        ", [$_SESSION['user_id']]);
    } else {
        $listId = $readLaterList['id'];
    }
    
    // Check if item already exists
    $existing = db_fetch_one("
        SELECT id FROM reading_list_items 
        WHERE list_id = ? AND item_type = ? AND item_id = ?
    ", [$listId, $itemType, $itemId]);
    
    if ($existing) {
        echo json_encode(['error' => 'Материал уже добавлен в список "Читать позже"']);
        exit;
    }
    
    // Add item
    $insertId = db_insert_id("
        INSERT INTO reading_list_items (list_id, item_type, item_id)
        VALUES (?, ?, ?)
    ", [$listId, $itemType, $itemId]);
    
    if ($insertId) {
        echo json_encode(['success' => true, 'message' => 'Добавлено в список "Читать позже"']);
    } else {
        echo json_encode(['error' => 'Ошибка при добавлении']);
    }
    
} elseif ($action === 'remove_from_list' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Remove item from reading list
    $input = json_decode(file_get_contents('php://input'), true);
    
    $listId = intval($input['list_id'] ?? 0);
    $itemType = $input['item_type'] ?? '';
    $itemId = intval($input['item_id'] ?? 0);
    
    // Remove item (verify ownership)
    $success = db_execute("
        DELETE FROM reading_list_items 
        WHERE list_id = ? AND item_type = ? AND item_id = ?
        AND list_id IN (
            SELECT id FROM reading_lists WHERE user_id = ?
        )
    ", [$listId, $itemType, $itemId, $_SESSION['user_id']]);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Материал удален из списка']);
    } else {
        echo json_encode(['error' => 'Ошибка при удалении']);
    }
    
} elseif ($action === 'create_list' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create new reading list
    $input = json_decode(file_get_contents('php://input'), true);
    
    $name = trim($input['name'] ?? '');
    $description = trim($input['description'] ?? '');
    $isPublic = intval($input['is_public'] ?? 0);
    
    if (!$name) {
        echo json_encode(['error' => 'Введите название списка']);
        exit;
    }
    
    $listId = db_insert_id("
        INSERT INTO reading_lists (user_id, name, description, is_public)
        VALUES (?, ?, ?, ?)
    ", [$_SESSION['user_id'], $name, $description, $isPublic]);
    
    if ($listId) {
        echo json_encode([
            'success' => true, 
            'list_id' => $listId,
            'message' => 'Список создан'
        ]);
    } else {
        echo json_encode(['error' => 'Ошибка при создании списка']);
    }
    
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}
?>