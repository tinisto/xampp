<?php
// Notification system

class NotificationManager {
    
    // Create a new notification
    public static function create($userId, $type, $title, $message = null, $link = null) {
        return db_insert_id("
            INSERT INTO notifications (user_id, type, title, message, link)
            VALUES (?, ?, ?, ?, ?)
        ", [$userId, $type, $title, $message, $link]);
    }
    
    // Get unread count for user
    public static function getUnreadCount($userId) {
        return db_fetch_column("
            SELECT COUNT(*) FROM notifications 
            WHERE user_id = ? AND is_read = 0
        ", [$userId]);
    }
    
    // Get recent notifications for user
    public static function getRecent($userId, $limit = 10) {
        return db_fetch_all("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ", [$userId, $limit]);
    }
    
    // Mark notification as read
    public static function markAsRead($notificationId, $userId) {
        return db_execute("
            UPDATE notifications 
            SET is_read = 1 
            WHERE id = ? AND user_id = ?
        ", [$notificationId, $userId]);
    }
    
    // Mark all notifications as read for user
    public static function markAllAsRead($userId) {
        return db_execute("
            UPDATE notifications 
            SET is_read = 1 
            WHERE user_id = ? AND is_read = 0
        ", [$userId]);
    }
    
    // Clean up old notifications (older than 30 days)
    public static function cleanup() {
        return db_execute("
            DELETE FROM notifications 
            WHERE created_at < datetime('now', '-30 days')
        ");
    }
    
    // Specific notification types
    public static function notifyNewComment($authorId, $commenterName, $itemTitle, $itemType, $itemId) {
        $link = null;
        switch ($itemType) {
            case 'news':
                $link = "/news/novost-{$itemId}";
                break;
            case 'post':
                $link = "/post/statya-{$itemId}";
                break;
        }
        
        return self::create(
            $authorId,
            'comment',
            'Новый комментарий',
            "{$commenterName} оставил комментарий к материалу \"{$itemTitle}\"",
            $link
        );
    }
    
    public static function notifyNewRating($authorId, $raterName, $itemTitle, $rating, $itemType, $itemId) {
        $link = null;
        switch ($itemType) {
            case 'news':
                $link = "/news/novost-{$itemId}";
                break;
            case 'post':
                $link = "/post/statya-{$itemId}";
                break;
        }
        
        $stars = str_repeat('⭐', $rating);
        
        return self::create(
            $authorId,
            'rating',
            'Новая оценка',
            "{$raterName} поставил оценку {$stars} материалу \"{$itemTitle}\"",
            $link
        );
    }
    
    public static function notifyAddedToFavorites($authorId, $userName, $itemTitle, $itemType, $itemId) {
        $link = null;
        switch ($itemType) {
            case 'news':
                $link = "/news/novost-{$itemId}";
                break;
            case 'post':
                $link = "/post/statya-{$itemId}";
                break;
        }
        
        return self::create(
            $authorId,
            'favorite',
            'Материал добавлен в избранное',
            "{$userName} добавил в избранное материал \"{$itemTitle}\"",
            $link
        );
    }
    
    public static function notifyNewNews($title, $newsId) {
        // Notify all users (or specific subscribers) about new news
        $users = db_fetch_all("SELECT id FROM users WHERE role = 'admin'"); // For now, only admins
        
        foreach ($users as $user) {
            self::create(
                $user['id'],
                'news',
                'Опубликована новая новость',
                "Новая новость: \"{$title}\"",
                "/news/novost-{$newsId}"
            );
        }
    }
    
    public static function notifySystemMessage($userId, $title, $message, $link = null) {
        return self::create(
            $userId,
            'system',
            $title,
            $message,
            $link
        );
    }
    
    // Send welcome notification to new users
    public static function sendWelcomeNotification($userId, $userName) {
        return self::create(
            $userId,
            'system',
            'Добро пожаловать на 11klassniki.ru!',
            "Привет, {$userName}! Добро пожаловать на наш образовательный портал. Изучайте материалы, создавайте списки для чтения и участвуйте в обсуждениях.",
            '/profile'
        );
    }
    
    // Event-related notifications
    public static function notifyEventSubscription($userId, $event) {
        return self::create(
            $userId,
            'event',
            'Подписка на событие оформлена',
            "Вы подписались на уведомления о событии \"{$event['title']}\"",
            "/event/{$event['id']}"
        );
    }
    
    public static function notifyEventReminder($userId, $event) {
        $eventDate = date('d.m.Y', strtotime($event['start_date']));
        $eventTime = $event['start_time'] ? date('H:i', strtotime($event['start_time'])) : '';
        
        $message = "Напоминание: событие \"{$event['title']}\" состоится ";
        if ($event['start_date'] === date('Y-m-d')) {
            $message .= "сегодня";
        } else {
            $message .= $eventDate;
        }
        if ($eventTime) {
            $message .= " в {$eventTime}";
        }
        if ($event['location']) {
            $message .= " ({$event['location']})";
        }
        
        return self::create(
            $userId,
            'event_reminder',
            'Напоминание о событии',
            $message,
            "/event/{$event['event_id']}"
        );
    }
    
    public static function notifyNewEvent($eventTitle, $eventId, $eventType) {
        // Notify users interested in this type of event
        $typeLabel = [
            'deadline' => 'дедлайн',
            'exam' => 'экзамен', 
            'open_day' => 'день открытых дверей',
            'conference' => 'конференция',
            'other' => 'событие'
        ];
        
        $users = db_fetch_all("SELECT id FROM users WHERE is_active = 1"); // All active users
        
        foreach ($users as $user) {
            self::create(
                $user['id'],
                'event',
                'Новое событие',
                "Добавлено новое событие: {$typeLabel[$eventType] ?? 'событие'} \"{$eventTitle}\"",
                "/event/{$eventId}"
            );
        }
    }
}

// Function to include notification badge in header
function include_notification_badge() {
    if (!isset($_SESSION['user_id'])) {
        return '';
    }
    
    $unreadCount = NotificationManager::getUnreadCount($_SESSION['user_id']);
    
    if ($unreadCount > 0) {
        return "
        <a href='/notifications' style='position: relative; color: var(--text-primary); text-decoration: none; margin-right: 10px;'>
            <i class='fas fa-bell' style='font-size: 18px;'></i>
            <span class='notification-badge' style='position: absolute; top: -8px; right: -8px; 
                                                    background: #dc3545; color: white; border-radius: 10px; 
                                                    padding: 2px 6px; font-size: 12px; font-weight: bold; 
                                                    min-width: 16px; text-align: center;'>
                {$unreadCount}
            </span>
        </a>";
    } else {
        return "
        <a href='/notifications' style='color: var(--text-secondary); text-decoration: none; margin-right: 10px;'>
            <i class='fas fa-bell' style='font-size: 18px;'></i>
        </a>";
    }
}
?>