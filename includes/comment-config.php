<?php
/**
 * Dynamic Comment Configuration Loader
 * Loads configuration from auto-tune system
 */

// Load configuration file
function getCommentConfig() {
    $configFile = $_SERVER['DOCUMENT_ROOT'] . '/config/comment-limits.json';
    
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
            'duplicate_threshold' => 0.8
        ],
        'auto_tune' => [
            'enabled' => true,
            'learning_period_days' => 7,
            'adjustment_factor' => 0.2,
            'min_sample_size' => 100
        ]
    ];
    
    if (file_exists($configFile)) {
        $config = json_decode(file_get_contents($configFile), true);
        if ($config) {
            return array_merge_recursive($defaultConfig, $config);
        }
    }
    
    return $defaultConfig;
}

// Check rate limits
function checkRateLimits($connection, $clientIP, $config = null) {
    if (!$config) {
        $config = getCommentConfig();
    }
    
    $limits = $config['rate_limits'];
    
    // Check per minute limit
    $query = "SELECT COUNT(*) as count FROM comments 
              WHERE author_ip = ? AND date >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $clientIP);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] >= $limits['comments_per_minute']) {
        return ['success' => false, 'error' => 'Слишком много комментариев. Подождите минуту.'];
    }
    
    // Check per hour limit
    $query = "SELECT COUNT(*) as count FROM comments 
              WHERE author_ip = ? AND date >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $clientIP);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] >= $limits['comments_per_hour']) {
        return ['success' => false, 'error' => 'Превышен часовой лимит комментариев.'];
    }
    
    // Check per day limit
    $query = "SELECT COUNT(*) as count FROM comments 
              WHERE author_ip = ? AND date >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $clientIP);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] >= $limits['comments_per_day']) {
        return ['success' => false, 'error' => 'Превышен дневной лимит комментариев.'];
    }
    
    return ['success' => true];
}

// Check spam filters
function checkSpamFilters($comment, $config = null) {
    if (!$config) {
        $config = getCommentConfig();
    }
    
    $filters = $config['spam_filters'];
    
    // Check length
    $length = mb_strlen($comment);
    if ($length < $filters['min_comment_length']) {
        return ['success' => false, 'error' => "Комментарий слишком короткий (минимум {$filters['min_comment_length']} символа)"];
    }
    
    if ($length > $filters['max_comment_length']) {
        return ['success' => false, 'error' => "Комментарий слишком длинный (максимум {$filters['max_comment_length']} символов)"];
    }
    
    // Check spam keywords
    $commentLower = mb_strtolower($comment);
    foreach ($filters['spam_keywords'] as $keyword) {
        if (strpos($commentLower, $keyword) !== false) {
            return ['success' => false, 'error' => 'Комментарий содержит недопустимый контент'];
        }
    }
    
    // Check link count
    $linkCount = preg_match_all('/https?:\/\/[^\s]+/i', $comment);
    if ($linkCount > $filters['link_limit']) {
        return ['success' => false, 'error' => "Слишком много ссылок (максимум {$filters['link_limit']})"];
    }
    
    return ['success' => true];
}

// Check for duplicate content
function checkDuplicateContent($connection, $comment, $authorIP, $config = null) {
    if (!$config) {
        $config = getCommentConfig();
    }
    
    $threshold = $config['spam_filters']['duplicate_threshold'];
    
    // Get recent comments from same IP
    $query = "SELECT comment_text FROM comments 
              WHERE author_ip = ? AND date >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
              ORDER BY date DESC LIMIT 5";
    
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $authorIP);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $similarity = 0;
        similar_text($comment, $row['comment_text'], $similarity);
        
        if ($similarity / 100 >= $threshold) {
            return ['success' => false, 'error' => 'Похожий комментарий уже был отправлен'];
        }
    }
    
    return ['success' => true];
}

// Combined validation function
function validateComment($connection, $comment, $author, $email, $clientIP) {
    $config = getCommentConfig();
    
    // Basic validation
    if (empty($comment) || empty($author)) {
        return ['success' => false, 'error' => 'Все обязательные поля должны быть заполнены'];
    }
    
    // Author name validation
    if (mb_strlen($author) < 2 || mb_strlen($author) > 100) {
        return ['success' => false, 'error' => 'Имя должно быть от 2 до 100 символов'];
    }
    
    // Email validation if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Некорректный email адрес'];
    }
    
    // Check rate limits
    $rateCheck = checkRateLimits($connection, $clientIP, $config);
    if (!$rateCheck['success']) {
        return $rateCheck;
    }
    
    // Check spam filters
    $spamCheck = checkSpamFilters($comment, $config);
    if (!$spamCheck['success']) {
        return $spamCheck;
    }
    
    // Check duplicate content
    $dupCheck = checkDuplicateContent($connection, $comment, $clientIP, $config);
    if (!$dupCheck['success']) {
        return $dupCheck;
    }
    
    return ['success' => true];
}
?>