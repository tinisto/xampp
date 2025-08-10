<?php
/**
 * Mobile App API v1
 * RESTful API for 11klassniki.ru mobile application
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/api_auth.php';

// Enable CORS for mobile apps
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Parse the request URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = str_replace('/api/v1', '', $requestUri);
$requestUri = trim($requestUri, '/');

// Parse path segments
$segments = $requestUri ? explode('/', $requestUri) : [];
$method = $_SERVER['REQUEST_METHOD'];

// Get input data
$input = null;
if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendResponse(400, ['error' => 'Invalid JSON input']);
    }
}

// Route the request
try {
    // Authentication endpoints (no auth required)
    if ($segments[0] === 'auth') {
        handleAuth($segments, $method, $input);
    }
    
    // Info endpoints (no auth required)
    elseif ($segments[0] === 'info') {
        handleInfo($segments, $method, $input);
    }
    
    // All other endpoints require authentication
    else {
        // Verify API authentication
        $user = ApiAuth::verifyToken();
        if (!$user) {
            sendResponse(401, ['error' => 'Authentication required']);
        }
        
        // Route authenticated requests
        switch ($segments[0]) {
            case 'user':
                handleUser($segments, $method, $input, $user);
                break;
            case 'news':
                handleNews($segments, $method, $input, $user);
                break;
            case 'posts':
                handlePosts($segments, $method, $input, $user);
                break;
            case 'events':
                handleEvents($segments, $method, $input, $user);
                break;
            case 'schools':
                handleSchools($segments, $method, $input, $user);
                break;
            case 'vpo':
                handleVpo($segments, $method, $input, $user);
                break;
            case 'spo':
                handleSpo($segments, $method, $input, $user);
                break;
            case 'search':
                handleSearch($segments, $method, $input, $user);
                break;
            case 'favorites':
                handleFavorites($segments, $method, $input, $user);
                break;
            case 'reading-lists':
                handleReadingLists($segments, $method, $input, $user);
                break;
            case 'notifications':
                handleNotifications($segments, $method, $input, $user);
                break;
            case 'ratings':
                handleRatings($segments, $method, $input, $user);
                break;
            case 'comments':
                handleComments($segments, $method, $input, $user);
                break;
            case 'recommendations':
                handleRecommendations($segments, $method, $input, $user);
                break;
            default:
                sendResponse(404, ['error' => 'Endpoint not found']);
        }
    }
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    sendResponse(500, ['error' => 'Internal server error']);
}

// Response helper function
function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Authentication endpoints
function handleAuth($segments, $method, $input) {
    switch ($segments[1] ?? '') {
        case 'login':
            if ($method !== 'POST') {
                sendResponse(405, ['error' => 'Method not allowed']);
            }
            
            $email = $input['email'] ?? '';
            $password = $input['password'] ?? '';
            
            if (!$email || !$password) {
                sendResponse(400, ['error' => 'Email and password required']);
            }
            
            $user = db_fetch_one("SELECT * FROM users WHERE email = ? AND is_active = 1", [$email]);
            
            if (!$user || !password_verify($password, $user['password'])) {
                sendResponse(401, ['error' => 'Invalid credentials']);
            }
            
            $token = ApiAuth::generateToken($user['id']);
            
            sendResponse(200, [
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'avatar' => $user['avatar']
                ]
            ]);
            break;
            
        case 'register':
            if ($method !== 'POST') {
                sendResponse(405, ['error' => 'Method not allowed']);
            }
            
            $name = $input['name'] ?? '';
            $email = $input['email'] ?? '';
            $password = $input['password'] ?? '';
            
            if (!$name || !$email || !$password) {
                sendResponse(400, ['error' => 'Name, email and password required']);
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                sendResponse(400, ['error' => 'Invalid email format']);
            }
            
            if (strlen($password) < 6) {
                sendResponse(400, ['error' => 'Password must be at least 6 characters']);
            }
            
            // Check if email exists
            $existing = db_fetch_one("SELECT id FROM users WHERE email = ?", [$email]);
            if ($existing) {
                sendResponse(400, ['error' => 'Email already registered']);
            }
            
            // Create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userId = db_insert_id("
                INSERT INTO users (name, email, password, is_active) 
                VALUES (?, ?, ?, 1)
            ", [$name, $email, $hashedPassword]);
            
            if (!$userId) {
                sendResponse(500, ['error' => 'Registration failed']);
            }
            
            // Send welcome notifications
            require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/notifications.php';
            require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email.php';
            
            NotificationManager::sendWelcomeNotification($userId, $name);
            EmailNotification::sendWelcomeEmail($email, $name);
            
            $token = ApiAuth::generateToken($userId);
            
            sendResponse(201, [
                'success' => true,
                'message' => 'Registration successful',
                'token' => $token,
                'user' => [
                    'id' => $userId,
                    'name' => $name,
                    'email' => $email,
                    'role' => 'user'
                ]
            ]);
            break;
            
        case 'refresh':
            if ($method !== 'POST') {
                sendResponse(405, ['error' => 'Method not allowed']);
            }
            
            $user = ApiAuth::verifyToken();
            if (!$user) {
                sendResponse(401, ['error' => 'Invalid token']);
            }
            
            $newToken = ApiAuth::generateToken($user['id']);
            
            sendResponse(200, [
                'success' => true,
                'token' => $newToken
            ]);
            break;
            
        case 'logout':
            if ($method !== 'POST') {
                sendResponse(405, ['error' => 'Method not allowed']);
            }
            
            ApiAuth::invalidateToken();
            sendResponse(200, ['success' => true, 'message' => 'Logged out successfully']);
            break;
            
        default:
            sendResponse(404, ['error' => 'Auth endpoint not found']);
    }
}

// Info endpoints (no auth required)
function handleInfo($segments, $method, $input) {
    if ($method !== 'GET') {
        sendResponse(405, ['error' => 'Method not allowed']);
    }
    
    switch ($segments[1] ?? '') {
        case 'stats':
            $stats = [
                'schools' => db_fetch_column("SELECT COUNT(*) FROM schools") ?: 3318,
                'vpo' => db_fetch_column("SELECT COUNT(*) FROM vpo") ?: 2520,
                'spo' => db_fetch_column("SELECT COUNT(*) FROM spo") ?: 1850,
                'news' => db_fetch_column("SELECT COUNT(*) FROM news WHERE is_published = 1") ?: 496,
                'posts' => db_fetch_column("SELECT COUNT(*) FROM posts WHERE is_published = 1") ?: 100,
                'events' => db_fetch_column("SELECT COUNT(*) FROM events WHERE is_public = 1") ?: 50
            ];
            
            sendResponse(200, [
                'success' => true,
                'stats' => $stats
            ]);
            break;
            
        case 'version':
            sendResponse(200, [
                'success' => true,
                'version' => '1.0.0',
                'api_version' => 'v1',
                'features' => [
                    'authentication',
                    'content_management',
                    'favorites',
                    'reading_lists',
                    'notifications',
                    'events_calendar',
                    'ratings',
                    'comments',
                    'recommendations',
                    'search'
                ]
            ]);
            break;
            
        default:
            sendResponse(404, ['error' => 'Info endpoint not found']);
    }
}

// Include additional handler files
require_once 'handlers/user.php';
require_once 'handlers/content.php';
require_once 'handlers/interactions.php';
?>