<?php
/**
 * Cookie Consent Processing
 * Handles AJAX requests for cookie consent
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$consent_level = $input['level'] ?? 'essential';

if (!in_array($consent_level, ['essential', 'all'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid consent level']);
    exit;
}

try {
    $expires = time() + (365 * 24 * 60 * 60); // 1 year
    
    $cookie_options = [
        'expires' => $expires,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ];
    
    // Set consent cookie
    setcookie('cookie_consent', $consent_level, $cookie_options);
    
    // Set analytics consent
    $analytics_consent = ($consent_level === 'all') ? 'true' : 'false';
    setcookie('analytics_consent', $analytics_consent, $cookie_options);
    
    // Log consent for compliance
    error_log("Cookie consent: {$consent_level} from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    
    echo json_encode([
        'success' => true,
        'level' => $consent_level,
        'message' => 'Настройки cookie сохранены'
    ]);
    
} catch (Exception $e) {
    error_log("Cookie consent error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сохранения настроек']);
}
?>