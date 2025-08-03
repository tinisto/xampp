<?php
/**
 * Admin Notifications System
 * Functions to get counts of items that need admin attention
 */

function getAdminNotificationCounts($connection) {
    $notifications = [
        'messages' => 0,
        'comments' => 0,
        'users' => 0,
        'total' => 0
    ];
    
    try {
        // Count new users registered today
        $usersQuery = "SELECT COUNT(*) as count FROM users WHERE DATE(registration_date) = CURDATE()";
        $usersResult = mysqli_query($connection, $usersQuery);
        if ($usersResult) {
            $usersData = mysqli_fetch_assoc($usersResult);
            $notifications['users'] = (int)$usersData['count'];
        }
        
        // Count recent comments (last 24 hours) - assuming comments table exists
        $commentsQuery = "SELECT COUNT(*) as count FROM comments WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
        $commentsResult = mysqli_query($connection, $commentsQuery);
        if ($commentsResult) {
            $commentsData = mysqli_fetch_assoc($commentsResult);
            $notifications['comments'] = (int)$commentsData['count'];
        }
        
        // For messages, we'll create a simple test count for now
        // This can be updated later when you have a proper messages system
        $notifications['messages'] = 0; // Set to 0 for now
        
        // Calculate total
        $notifications['total'] = $notifications['messages'] + $notifications['comments'] + $notifications['users'];
        
    } catch (Exception $e) {
        // If there's an error, return empty counts
        error_log("Admin notifications error: " . $e->getMessage());
    }
    
    return $notifications;
}

function renderAdminNotificationBadge($count, $type = 'primary') {
    if ($count > 0) {
        $displayCount = $count > 99 ? '99+' : $count;
        return "<span class=\"notification-badge notification-badge-{$type}\">{$displayCount}</span>";
    }
    return '';
}
?>