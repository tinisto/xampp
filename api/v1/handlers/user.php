<?php
/**
 * User management API handlers
 */

function handleUser($segments, $method, $input, $user) {
    $endpoint = $segments[1] ?? '';
    
    switch ($endpoint) {
        case 'profile':
            handleUserProfile($method, $input, $user);
            break;
        case 'settings':
            handleUserSettings($method, $input, $user);
            break;
        case 'avatar':
            handleUserAvatar($method, $input, $user);
            break;
        case 'password':
            handleUserPassword($method, $input, $user);
            break;
        case 'stats':
            handleUserStats($method, $input, $user);
            break;
        default:
            sendResponse(404, ['error' => 'User endpoint not found']);
    }
}

function handleUserProfile($method, $input, $user) {
    switch ($method) {
        case 'GET':
            // Get user profile
            $profile = db_fetch_one("
                SELECT id, name, email, role, avatar, bio, location, website, 
                       created_at, last_login
                FROM users WHERE id = ?
            ", [$user['id']]);
            
            // Get user statistics
            $stats = [
                'favorites_count' => db_fetch_column("
                    SELECT COUNT(*) FROM favorites WHERE user_id = ?
                ", [$user['id']]),
                'comments_count' => db_fetch_column("
                    SELECT COUNT(*) FROM comments WHERE user_id = ?
                ", [$user['id']]),
                'reading_lists_count' => db_fetch_column("
                    SELECT COUNT(*) FROM reading_lists WHERE user_id = ?
                ", [$user['id']]),
                'ratings_given' => db_fetch_column("
                    SELECT COUNT(*) FROM ratings WHERE user_id = ?
                ", [$user['id']])
            ];
            
            sendResponse(200, [
                'success' => true,
                'profile' => $profile,
                'stats' => $stats
            ]);
            break;
            
        case 'PUT':
            // Update user profile
            $allowedFields = ['name', 'bio', 'location', 'website'];
            $updateData = [];
            $params = [];
            
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    $updateData[] = "$field = ?";
                    $params[] = $input[$field];
                }
            }
            
            if (empty($updateData)) {
                sendResponse(400, ['error' => 'No valid fields to update']);
            }
            
            $params[] = $user['id'];
            
            $result = db_query("
                UPDATE users SET " . implode(', ', $updateData) . " 
                WHERE id = ?
            ", $params);
            
            if ($result) {
                sendResponse(200, [
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ]);
            } else {
                sendResponse(500, ['error' => 'Failed to update profile']);
            }
            break;
            
        default:
            sendResponse(405, ['error' => 'Method not allowed']);
    }
}

function handleUserSettings($method, $input, $user) {
    switch ($method) {
        case 'GET':
            // Get user settings (for now, return defaults)
            $settings = [
                'email_notifications' => true,
                'push_notifications' => true,
                'privacy_level' => 'public',
                'theme' => 'light',
                'language' => 'ru'
            ];
            
            sendResponse(200, [
                'success' => true,
                'settings' => $settings
            ]);
            break;
            
        case 'PUT':
            // Update user settings
            // In a real implementation, store these in a user_settings table
            sendResponse(200, [
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
            break;
            
        default:
            sendResponse(405, ['error' => 'Method not allowed']);
    }
}

function handleUserAvatar($method, $input, $user) {
    switch ($method) {
        case 'POST':
            // Upload avatar
            if (!isset($_FILES['avatar'])) {
                sendResponse(400, ['error' => 'No avatar file uploaded']);
            }
            
            require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/upload.php';
            
            try {
                $avatarPath = ImageUpload::handleUpload($_FILES['avatar'], 'avatar', $user['id']);
                
                // Update user avatar in database
                db_query("UPDATE users SET avatar = ? WHERE id = ?", [$avatarPath, $user['id']]);
                
                sendResponse(200, [
                    'success' => true,
                    'message' => 'Avatar updated successfully',
                    'avatar_url' => $avatarPath
                ]);
            } catch (Exception $e) {
                sendResponse(400, ['error' => $e->getMessage()]);
            }
            break;
            
        case 'DELETE':
            // Remove avatar
            db_query("UPDATE users SET avatar = NULL WHERE id = ?", [$user['id']]);
            
            sendResponse(200, [
                'success' => true,
                'message' => 'Avatar removed successfully'
            ]);
            break;
            
        default:
            sendResponse(405, ['error' => 'Method not allowed']);
    }
}

function handleUserPassword($method, $input, $user) {
    if ($method !== 'PUT') {
        sendResponse(405, ['error' => 'Method not allowed']);
    }
    
    $currentPassword = $input['current_password'] ?? '';
    $newPassword = $input['new_password'] ?? '';
    
    if (!$currentPassword || !$newPassword) {
        sendResponse(400, ['error' => 'Current and new passwords required']);
    }
    
    // Verify current password
    if (!password_verify($currentPassword, $user['password'])) {
        sendResponse(400, ['error' => 'Current password is incorrect']);
    }
    
    if (strlen($newPassword) < 6) {
        sendResponse(400, ['error' => 'New password must be at least 6 characters']);
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $result = db_query("UPDATE users SET password = ? WHERE id = ?", [$hashedPassword, $user['id']]);
    
    if ($result) {
        sendResponse(200, [
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    } else {
        sendResponse(500, ['error' => 'Failed to update password']);
    }
}

function handleUserStats($method, $input, $user) {
    if ($method !== 'GET') {
        sendResponse(405, ['error' => 'Method not allowed']);
    }
    
    // Get comprehensive user statistics
    $stats = [
        'content' => [
            'favorites_count' => db_fetch_column("
                SELECT COUNT(*) FROM favorites WHERE user_id = ?
            ", [$user['id']]),
            'comments_count' => db_fetch_column("
                SELECT COUNT(*) FROM comments WHERE user_id = ?
            ", [$user['id']]),
            'ratings_given' => db_fetch_column("
                SELECT COUNT(*) FROM ratings WHERE user_id = ?
            ", [$user['id']]),
            'reading_lists_count' => db_fetch_column("
                SELECT COUNT(*) FROM reading_lists WHERE user_id = ?
            ", [$user['id']]),
            'reading_list_items' => db_fetch_column("
                SELECT COUNT(*) FROM reading_list_items rli
                JOIN reading_lists rl ON rli.list_id = rl.id
                WHERE rl.user_id = ?
            ", [$user['id']])
        ],
        'events' => [
            'subscriptions' => db_fetch_column("
                SELECT COUNT(*) FROM event_subscriptions WHERE user_id = ?
            ", [$user['id']]),
            'upcoming_events' => db_fetch_column("
                SELECT COUNT(*) FROM event_subscriptions es
                JOIN events e ON es.event_id = e.id
                WHERE es.user_id = ? AND e.start_date >= CURRENT_DATE
            ", [$user['id']])
        ],
        'activity' => [
            'unread_notifications' => db_fetch_column("
                SELECT COUNT(*) FROM notifications 
                WHERE user_id = ? AND is_read = 0
            ", [$user['id']]),
            'member_since' => $user['created_at'],
            'last_login' => $user['last_login']
        ]
    ];
    
    // Get favorite categories
    $favoriteCategories = db_fetch_all("
        SELECT c.name, COUNT(*) as count
        FROM favorites f
        JOIN (
            SELECT 'news' as type, id_news as id, category_id
            FROM news
            UNION ALL
            SELECT 'post' as type, id, category
            FROM posts
        ) content ON f.item_id = content.id AND f.item_type = content.type
        JOIN categories c ON content.category_id = c.id
        WHERE f.user_id = ?
        GROUP BY c.id, c.name
        ORDER BY count DESC
        LIMIT 5
    ", [$user['id']]);
    
    $stats['preferences'] = [
        'favorite_categories' => $favoriteCategories
    ];
    
    sendResponse(200, [
        'success' => true,
        'stats' => $stats
    ]);
}
?>