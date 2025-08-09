<?php
/**
 * Auto-tuning System for Rate Limits and Spam Filters
 * Dynamically adjusts thresholds based on system behavior
 */

session_start();
header('Content-Type: application/json');

// Check admin access
if ((!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') && 
    (!isset($_SESSION['occupation']) || $_SESSION['occupation'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Configuration file path
define('CONFIG_FILE', $_SERVER['DOCUMENT_ROOT'] . '/config/comment-limits.json');

// Default configuration
$defaultConfig = [
    'rate_limits' => [
        'comments_per_minute' => 3,
        'comments_per_hour' => 20,
        'comments_per_day' => 100
    ],
    'spam_filters' => [
        'min_comment_length' => 3,
        'max_comment_length' => 2000,
        'spam_keywords' => ['spam', 'casino', 'viagra', 'porn', 'xxx', 'bet', 'loan'],
        'link_limit' => 2,
        'duplicate_threshold' => 0.8 // 80% similarity
    ],
    'auto_tune' => [
        'enabled' => true,
        'learning_period_days' => 7,
        'adjustment_factor' => 0.2, // 20% adjustment max
        'min_sample_size' => 100
    ],
    'last_updated' => date('Y-m-d H:i:s')
];

// Load current configuration
function loadConfig() {
    global $defaultConfig;
    
    if (file_exists(CONFIG_FILE)) {
        $config = json_decode(file_get_contents(CONFIG_FILE), true);
        return $config ?: $defaultConfig;
    }
    
    // Create config directory if needed
    $configDir = dirname(CONFIG_FILE);
    if (!is_dir($configDir)) {
        mkdir($configDir, 0755, true);
    }
    
    saveConfig($defaultConfig);
    return $defaultConfig;
}

// Save configuration
function saveConfig($config) {
    $config['last_updated'] = date('Y-m-d H:i:s');
    file_put_contents(CONFIG_FILE, json_encode($config, JSON_PRETTY_PRINT));
}

// Get action
$action = $_GET['action'] ?? 'analyze';

try {
    $currentConfig = loadConfig();
    
    switch ($action) {
        case 'analyze':
            // Analyze system behavior and suggest adjustments
            $analysis = analyzeSystemBehavior($connection, $currentConfig);
            
            echo json_encode([
                'success' => true,
                'current_config' => $currentConfig,
                'analysis' => $analysis,
                'recommendations' => generateRecommendations($analysis, $currentConfig)
            ]);
            break;
            
        case 'apply':
            // Apply recommended adjustments
            if (!$currentConfig['auto_tune']['enabled']) {
                throw new Exception('Auto-tuning is disabled');
            }
            
            $analysis = analyzeSystemBehavior($connection, $currentConfig);
            $newConfig = applyRecommendations($analysis, $currentConfig);
            
            saveConfig($newConfig);
            
            // Log the changes
            logConfigChange($connection, $currentConfig, $newConfig);
            
            echo json_encode([
                'success' => true,
                'message' => 'Configuration updated successfully',
                'old_config' => $currentConfig,
                'new_config' => $newConfig
            ]);
            break;
            
        case 'toggle':
            // Enable/disable auto-tuning
            $currentConfig['auto_tune']['enabled'] = !$currentConfig['auto_tune']['enabled'];
            saveConfig($currentConfig);
            
            echo json_encode([
                'success' => true,
                'enabled' => $currentConfig['auto_tune']['enabled'],
                'message' => $currentConfig['auto_tune']['enabled'] ? 'Auto-tuning enabled' : 'Auto-tuning disabled'
            ]);
            break;
            
        case 'reset':
            // Reset to defaults
            saveConfig($defaultConfig);
            
            echo json_encode([
                'success' => true,
                'message' => 'Configuration reset to defaults',
                'config' => $defaultConfig
            ]);
            break;
            
        case 'manual':
            // Manual configuration update
            $updates = json_decode(file_get_contents('php://input'), true);
            
            if ($updates) {
                $newConfig = array_merge_recursive($currentConfig, $updates);
                saveConfig($newConfig);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Configuration updated manually',
                    'config' => $newConfig
                ]);
            } else {
                throw new Exception('No updates provided');
            }
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// Analyze system behavior
function analyzeSystemBehavior($connection, $config) {
    $analysis = [];
    $period = $config['auto_tune']['learning_period_days'];
    
    // Analyze comment velocity
    $velocityQuery = "SELECT 
        COUNT(*) as total_comments,
        COUNT(DISTINCT author_ip) as unique_ips,
        COUNT(DISTINCT user_id) as unique_users,
        MAX(comment_count) as max_per_ip,
        AVG(comment_count) as avg_per_ip
        FROM (
            SELECT author_ip, COUNT(*) as comment_count
            FROM comments
            WHERE date >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY author_ip
        ) as ip_stats";
    
    $stmt = $connection->prepare($velocityQuery);
    $stmt->bind_param("i", $period);
    $stmt->execute();
    $analysis['velocity'] = $stmt->get_result()->fetch_assoc();
    
    // Analyze spam patterns
    $spamQuery = "SELECT 
        COUNT(*) as total_spam_attempts,
        COUNT(DISTINCT author_ip) as spam_ips
        FROM comments
        WHERE date >= DATE_SUB(NOW(), INTERVAL ? DAY)
        AND (is_approved = 0 OR comment_text REGEXP ?)";
    
    $spamPattern = implode('|', $config['spam_filters']['spam_keywords']);
    $stmt = $connection->prepare($spamQuery);
    $stmt->bind_param("is", $period, $spamPattern);
    $stmt->execute();
    $analysis['spam'] = $stmt->get_result()->fetch_assoc();
    
    // Analyze rate limit violations
    $rateQuery = "SELECT 
        COUNT(*) as violations,
        MAX(comments_per_minute) as max_rate
        FROM (
            SELECT 
                author_ip,
                DATE_FORMAT(date, '%Y-%m-%d %H:%i') as minute,
                COUNT(*) as comments_per_minute
            FROM comments
            WHERE date >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY author_ip, minute
            HAVING comments_per_minute > ?
        ) as violations";
    
    $currentLimit = $config['rate_limits']['comments_per_minute'];
    $stmt = $connection->prepare($rateQuery);
    $stmt->bind_param("ii", $period, $currentLimit);
    $stmt->execute();
    $analysis['rate_violations'] = $stmt->get_result()->fetch_assoc();
    
    // Analyze comment quality
    $qualityQuery = "SELECT 
        AVG(CHAR_LENGTH(comment_text)) as avg_length,
        MIN(CHAR_LENGTH(comment_text)) as min_length,
        MAX(CHAR_LENGTH(comment_text)) as max_length,
        AVG(likes / (likes + dislikes + 1)) as avg_quality_score,
        COUNT(CASE WHEN edited_at IS NOT NULL THEN 1 END) / COUNT(*) as edit_rate
        FROM comments
        WHERE date >= DATE_SUB(NOW(), INTERVAL ? DAY)
        AND is_approved = 1";
    
    $stmt = $connection->prepare($qualityQuery);
    $stmt->bind_param("i", $period);
    $stmt->execute();
    $analysis['quality'] = $stmt->get_result()->fetch_assoc();
    
    // Analyze time patterns
    $timeQuery = "SELECT 
        HOUR(date) as hour,
        COUNT(*) as comment_count
        FROM comments
        WHERE date >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY hour
        ORDER BY comment_count DESC
        LIMIT 1";
    
    $stmt = $connection->prepare($timeQuery);
    $stmt->bind_param("i", $period);
    $stmt->execute();
    $analysis['peak_hour'] = $stmt->get_result()->fetch_assoc();
    
    return $analysis;
}

// Generate recommendations based on analysis
function generateRecommendations($analysis, $config) {
    $recommendations = [];
    $factor = $config['auto_tune']['adjustment_factor'];
    
    // Rate limit recommendations
    if ($analysis['rate_violations']['violations'] > 10) {
        // Too many violations, might be too strict
        $increase = ceil($config['rate_limits']['comments_per_minute'] * $factor);
        $recommendations['rate_limits']['comments_per_minute'] = [
            'current' => $config['rate_limits']['comments_per_minute'],
            'suggested' => $config['rate_limits']['comments_per_minute'] + $increase,
            'reason' => 'High number of rate limit violations detected'
        ];
    } elseif ($analysis['rate_violations']['violations'] == 0 && $analysis['velocity']['max_per_ip'] > 10) {
        // No violations but high activity, might be too loose
        $decrease = ceil($config['rate_limits']['comments_per_minute'] * $factor);
        $recommendations['rate_limits']['comments_per_minute'] = [
            'current' => $config['rate_limits']['comments_per_minute'],
            'suggested' => max(1, $config['rate_limits']['comments_per_minute'] - $decrease),
            'reason' => 'No violations but high activity detected'
        ];
    }
    
    // Spam filter recommendations
    $spamRate = $analysis['spam']['total_spam_attempts'] / max(1, $analysis['velocity']['total_comments']);
    if ($spamRate > 0.1) { // More than 10% spam
        $recommendations['spam_filters']['stricter'] = [
            'current_spam_rate' => round($spamRate * 100, 2) . '%',
            'action' => 'Consider adding more spam keywords or decreasing link limit',
            'reason' => 'High spam rate detected'
        ];
    }
    
    // Comment length recommendations
    if ($analysis['quality']['avg_length'] < 50) {
        $recommendations['spam_filters']['min_comment_length'] = [
            'current' => $config['spam_filters']['min_comment_length'],
            'suggested' => 10,
            'reason' => 'Average comment length is very short'
        ];
    }
    
    // Peak hour recommendations
    if ($analysis['peak_hour']['comment_count'] > 100) {
        $recommendations['rate_limits']['peak_hour_adjustment'] = [
            'peak_hour' => $analysis['peak_hour']['hour'],
            'volume' => $analysis['peak_hour']['comment_count'],
            'suggestion' => 'Consider different rate limits for peak hours'
        ];
    }
    
    return $recommendations;
}

// Apply recommendations to configuration
function applyRecommendations($analysis, $config) {
    $newConfig = $config;
    $recommendations = generateRecommendations($analysis, $config);
    
    foreach ($recommendations as $section => $changes) {
        foreach ($changes as $key => $recommendation) {
            if (isset($recommendation['suggested'])) {
                $newConfig[$section][$key] = $recommendation['suggested'];
            }
        }
    }
    
    return $newConfig;
}

// Log configuration changes
function logConfigChange($connection, $oldConfig, $newConfig) {
    $changes = [
        'timestamp' => date('Y-m-d H:i:s'),
        'changes' => array_diff_assoc(
            json_decode(json_encode($newConfig), true),
            json_decode(json_encode($oldConfig), true)
        )
    ];
    
    // Store in database or log file
    error_log('Comment config auto-tuned: ' . json_encode($changes));
}
?>