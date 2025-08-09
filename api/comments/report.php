<?php
/**
 * Report Comment API Endpoint
 * Allows users to report inappropriate comments
 */

header('Content-Type: application/json');

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
$comment_id = (int)($input['comment_id'] ?? 0);
$reason = trim($input['reason'] ?? '');
$description = trim($input['description'] ?? '');
$reporter_id = (int)($_SESSION['user_id'] ?? 0);
$reporter_ip = $_SERVER['REMOTE_ADDR'];

// Validate reason
$valid_reasons = ['spam', 'offensive', 'other'];
if (!in_array($reason, $valid_reasons)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Недопустимая причина жалобы']);
    exit;
}

// Validate comment exists
$stmt = $connection->prepare("SELECT id FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Комментарий не найден']);
    exit;
}

// Check if already reported by this IP/user
$check_query = $reporter_id > 0 
    ? "SELECT id FROM comment_reports WHERE comment_id = ? AND reporter_id = ? AND status = 'pending'"
    : "SELECT id FROM comment_reports WHERE comment_id = ? AND reporter_ip = ? AND reporter_id IS NULL AND status = 'pending'";

$stmt = $connection->prepare($check_query);
if ($reporter_id > 0) {
    $stmt->bind_param("ii", $comment_id, $reporter_id);
} else {
    $stmt->bind_param("is", $comment_id, $reporter_ip);
}
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Вы уже отправили жалобу на этот комментарий']);
    exit;
}

// Rate limiting - max 5 reports per hour per IP
$rate_query = "SELECT COUNT(*) as count FROM comment_reports 
               WHERE reporter_ip = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
$stmt = $connection->prepare($rate_query);
$stmt->bind_param("s", $reporter_ip);
$stmt->execute();
$rate_result = $stmt->get_result()->fetch_assoc();

if ($rate_result['count'] >= 5) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Слишком много жалоб. Попробуйте позже']);
    exit;
}

// Insert report
try {
    $stmt = $connection->prepare("INSERT INTO comment_reports (comment_id, reporter_id, reporter_ip, reason, description) VALUES (?, ?, ?, ?, ?)");
    $reporter_id_param = $reporter_id > 0 ? $reporter_id : null;
    $stmt->bind_param("iisss", $comment_id, $reporter_id_param, $reporter_ip, $reason, $description);
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'Жалоба отправлена. Модераторы рассмотрят её в ближайшее время'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Ошибка при отправке жалобы']);
}
?>