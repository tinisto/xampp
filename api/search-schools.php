<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['query']) || !isset($input['type'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$query = trim($input['query']);
$type = $input['type'];

if (strlen($query) < 3) {
    echo json_encode(['success' => false, 'message' => 'Минимум 3 символа для поиска']);
    exit;
}

// Determine table and fields based on type
$table = '';
$nameField = '';
$shortNameField = '';
$idField = '';
$urlPrefix = '';

switch ($type) {
    case 'schools':
        $table = 'schools';
        $nameField = 'school_name';
        $shortNameField = 'short_name';
        $idField = 'id_school';
        $urlPrefix = '/school/';
        break;
    case 'vpo':
        $table = 'vpo';
        $nameField = 'vpo_name';
        $shortNameField = 'short_name';
        $idField = 'id_vpo';
        $urlPrefix = '/vpo/';
        break;
    case 'spo':
        $table = 'spo';
        $nameField = 'spo_name';
        $shortNameField = 'short_name';  
        $idField = 'id_spo';
        $urlPrefix = '/spo/';
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid institution type']);
        exit;
}

try {
    // Prepare search query with LIKE for partial matches
    $searchTerm = '%' . $query . '%';
    
    $sql = "SELECT i.{$idField} as id, i.{$nameField} as name, i.{$shortNameField} as short_name, 
                   i.address, r.region_name
            FROM {$table} i
            LEFT JOIN regions r ON i.id_region = r.id_region
            WHERE (i.{$nameField} LIKE ? OR i.{$shortNameField} LIKE ?)
            AND i.approved = 1
            ORDER BY 
                CASE 
                    WHEN i.{$nameField} LIKE ? THEN 1
                    WHEN i.{$shortNameField} LIKE ? THEN 2
                    ELSE 3
                END,
                i.{$nameField}
            LIMIT 20";
    
    $stmt = $connection->prepare($sql);
    $exactSearchTerm = $query . '%'; // For exact beginning matches (higher priority)
    $stmt->bind_param('ssss', $searchTerm, $searchTerm, $exactSearchTerm, $exactSearchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $schools = [];
    while ($row = $result->fetch_assoc()) {
        $schools[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'short_name' => $row['short_name'],
            'address' => $row['address'],
            'region' => $row['region_name'],
            'url' => $urlPrefix . $row['id']
        ];
    }
    
    $stmt->close();
    
    if (empty($schools)) {
        echo json_encode([
            'success' => false, 
            'schools' => [], 
            'message' => "По запросу \"{$query}\" ничего не найдено. Попробуйте изменить поисковый запрос или воспользуйтесь поиском по регионам."
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'schools' => $schools,
            'count' => count($schools),
            'message' => 'Найдено ' . count($schools) . ' результатов'
        ]);
    }
    
} catch (Exception $e) {
    error_log("School search error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка поиска']);
}
?>