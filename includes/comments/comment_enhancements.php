<?php
/**
 * Comment System Enhancements
 * Advanced features for the comment system
 */

class CommentEnhancements {
    
    /**
     * Add like/dislike functionality to comments
     * @param int $commentId Comment ID
     * @param int $userId User ID
     * @param string $action 'like' or 'dislike'
     * @return array Result with success status and new counts
     */
    public static function toggleLike($commentId, $userId, $action = 'like') {
        global $connection;
        
        // Validate inputs
        if (!in_array($action, ['like', 'dislike'])) {
            return ['success' => false, 'message' => 'Invalid action'];
        }
        
        // Check if user already has a reaction
        $checkQuery = "SELECT reaction_type FROM comment_reactions 
                       WHERE comment_id = ? AND user_id = ?";
        $checkStmt = mysqli_prepare($connection, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, 'ii', $commentId, $userId);
        mysqli_stmt_execute($checkStmt);
        $result = mysqli_stmt_get_result($checkStmt);
        $existingReaction = mysqli_fetch_assoc($result);
        
        if ($existingReaction) {
            if ($existingReaction['reaction_type'] === $action) {
                // Remove reaction if same action clicked
                $deleteQuery = "DELETE FROM comment_reactions 
                               WHERE comment_id = ? AND user_id = ?";
                $deleteStmt = mysqli_prepare($connection, $deleteQuery);
                mysqli_stmt_bind_param($deleteStmt, 'ii', $commentId, $userId);
                mysqli_stmt_execute($deleteStmt);
            } else {
                // Update reaction if different action clicked
                $updateQuery = "UPDATE comment_reactions 
                               SET reaction_type = ?, created_at = NOW() 
                               WHERE comment_id = ? AND user_id = ?";
                $updateStmt = mysqli_prepare($connection, $updateQuery);
                mysqli_stmt_bind_param($updateStmt, 'sii', $action, $commentId, $userId);
                mysqli_stmt_execute($updateStmt);
            }
        } else {
            // Add new reaction
            $insertQuery = "INSERT INTO comment_reactions (comment_id, user_id, reaction_type, created_at) 
                           VALUES (?, ?, ?, NOW())";
            $insertStmt = mysqli_prepare($connection, $insertQuery);
            mysqli_stmt_bind_param($insertStmt, 'iis', $commentId, $userId, $action);
            mysqli_stmt_execute($insertStmt);
        }
        
        // Get updated counts
        $counts = self::getReactionCounts($commentId);
        
        return [
            'success' => true,
            'likes' => $counts['likes'],
            'dislikes' => $counts['dislikes'],
            'user_reaction' => self::getUserReaction($commentId, $userId)
        ];
    }
    
    /**
     * Get reaction counts for a comment
     * @param int $commentId Comment ID
     * @return array Like and dislike counts
     */
    public static function getReactionCounts($commentId) {
        global $connection;
        
        $query = "SELECT 
                    SUM(CASE WHEN reaction_type = 'like' THEN 1 ELSE 0 END) as likes,
                    SUM(CASE WHEN reaction_type = 'dislike' THEN 1 ELSE 0 END) as dislikes
                  FROM comment_reactions 
                  WHERE comment_id = ?";
        
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $commentId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $counts = mysqli_fetch_assoc($result);
        
        return [
            'likes' => (int)($counts['likes'] ?? 0),
            'dislikes' => (int)($counts['dislikes'] ?? 0)
        ];
    }
    
    /**
     * Get user's reaction to a comment
     * @param int $commentId Comment ID
     * @param int $userId User ID
     * @return string|null User's reaction or null
     */
    public static function getUserReaction($commentId, $userId) {
        global $connection;
        
        $query = "SELECT reaction_type FROM comment_reactions 
                  WHERE comment_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $commentId, $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reaction = mysqli_fetch_assoc($result);
        
        return $reaction ? $reaction['reaction_type'] : null;
    }
    
    /**
     * Report a comment for moderation
     * @param int $commentId Comment ID
     * @param int $reporterId User ID reporting
     * @param string $reason Report reason
     * @return array Result with success status
     */
    public static function reportComment($commentId, $reporterId, $reason) {
        global $connection;
        
        // Check if user already reported this comment
        $checkQuery = "SELECT id FROM comment_reports 
                       WHERE comment_id = ? AND reporter_id = ?";
        $checkStmt = mysqli_prepare($connection, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, 'ii', $commentId, $reporterId);
        mysqli_stmt_execute($checkStmt);
        $existing = mysqli_stmt_get_result($checkStmt);
        
        if (mysqli_num_rows($existing) > 0) {
            return ['success' => false, 'message' => 'Вы уже пожаловались на этот комментарий'];
        }
        
        // Add report
        $insertQuery = "INSERT INTO comment_reports (comment_id, reporter_id, reason, created_at, status) 
                        VALUES (?, ?, ?, NOW(), 'pending')";
        $insertStmt = mysqli_prepare($connection, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, 'iis', $commentId, $reporterId, $reason);
        
        if (mysqli_stmt_execute($insertStmt)) {
            // Increment report count on comment
            $updateQuery = "UPDATE comments SET report_count = report_count + 1 WHERE id = ?";
            $updateStmt = mysqli_prepare($connection, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, 'i', $commentId);
            mysqli_stmt_execute($updateStmt);
            
            return ['success' => true, 'message' => 'Жалоба отправлена на модерацию'];
        }
        
        return ['success' => false, 'message' => 'Ошибка при отправке жалобы'];
    }
    
    /**
     * Pin/unpin a comment (admin only)
     * @param int $commentId Comment ID
     * @param bool $pinned Whether to pin or unpin
     * @return bool Success status
     */
    public static function togglePin($commentId, $pinned = true) {
        global $connection;
        
        $query = "UPDATE comments SET pinned = ? WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        $pinnedValue = $pinned ? 1 : 0;
        mysqli_stmt_bind_param($stmt, 'ii', $pinnedValue, $commentId);
        
        return mysqli_stmt_execute($stmt);
    }
    
    /**
     * Get comment thread with enhanced data
     * @param int $postId Post ID
     * @param int $userId Current user ID (optional)
     * @param array $options Display options
     * @return array Comments with enhanced data
     */
    public static function getEnhancedComments($postId, $userId = null, $options = []) {
        global $connection;
        
        $defaults = [
            'include_reactions' => true,
            'include_reports' => false,
            'order_by' => 'pinned DESC, created_at ASC',
            'limit' => 50
        ];
        
        $settings = array_merge($defaults, $options);
        
        $query = "SELECT c.*, u.username, u.avatar,
                         " . ($settings['include_reactions'] ? "
                         (SELECT COUNT(*) FROM comment_reactions cr WHERE cr.comment_id = c.id AND cr.reaction_type = 'like') as likes,
                         (SELECT COUNT(*) FROM comment_reactions cr WHERE cr.comment_id = c.id AND cr.reaction_type = 'dislike') as dislikes,
                         " . ($userId ? "(SELECT reaction_type FROM comment_reactions cr WHERE cr.comment_id = c.id AND cr.user_id = {$userId}) as user_reaction," : "") : "") . "
                         " . ($settings['include_reports'] ? "c.report_count," : "") . "
                         c.pinned
                  FROM comments c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = ? AND c.approved = 1
                  ORDER BY {$settings['order_by']}
                  LIMIT {$settings['limit']}";
        
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $postId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $comments = [];
        while ($comment = mysqli_fetch_assoc($result)) {
            // Format timestamps
            $comment['created_at_formatted'] = self::formatTimestamp($comment['created_at']);
            $comment['is_recent'] = (time() - strtotime($comment['created_at'])) < 300; // 5 minutes
            
            // Process content
            $comment['content_html'] = self::processCommentContent($comment['content']);
            
            $comments[] = $comment;
        }
        
        return $comments;
    }
    
    /**
     * Process comment content for display
     * @param string $content Raw comment content
     * @return string Processed HTML content
     */
    private static function processCommentContent($content) {
        // Sanitize content
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        
        // Convert links to clickable
        $content = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
            $content
        );
        
        // Convert line breaks
        $content = nl2br($content);
        
        // Convert @mentions to links (if user exists)
        $content = preg_replace_callback(
            '/@(\w+)/',
            function($matches) {
                global $connection;
                $username = $matches[1];
                
                $query = "SELECT id FROM users WHERE username = ? LIMIT 1";
                $stmt = mysqli_prepare($connection, $query);
                mysqli_stmt_bind_param($stmt, 's', $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    return '<a href="/profile/' . htmlspecialchars($username) . '" class="mention">@' . htmlspecialchars($username) . '</a>';
                }
                
                return $matches[0];
            },
            $content
        );
        
        return $content;
    }
    
    /**
     * Format timestamp for display
     * @param string $timestamp Database timestamp
     * @return string Formatted timestamp
     */
    private static function formatTimestamp($timestamp) {
        $time = strtotime($timestamp);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return 'только что';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' ' . self::pluralize($minutes, 'минуту', 'минуты', 'минут') . ' назад';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' ' . self::pluralize($hours, 'час', 'часа', 'часов') . ' назад';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' ' . self::pluralize($days, 'день', 'дня', 'дней') . ' назад';
        } else {
            return date('d.m.Y в H:i', $time);
        }
    }
    
    /**
     * Russian pluralization helper
     * @param int $number Number
     * @param string $one Form for 1
     * @param string $few Form for 2-4
     * @param string $many Form for 5+
     * @return string Correct form
     */
    private static function pluralize($number, $one, $few, $many) {
        if ($number % 10 == 1 && $number % 100 != 11) {
            return $one;
        } elseif ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20)) {
            return $few;
        } else {
            return $many;
        }
    }
    
    /**
     * Get comment statistics for admin
     * @return array Comment statistics
     */
    public static function getCommentStats() {
        global $connection;
        
        $stats = [];
        
        // Total comments
        $result = mysqli_query($connection, "SELECT COUNT(*) as total FROM comments");
        $stats['total_comments'] = mysqli_fetch_assoc($result)['total'];
        
        // Approved comments
        $result = mysqli_query($connection, "SELECT COUNT(*) as approved FROM comments WHERE approved = 1");
        $stats['approved_comments'] = mysqli_fetch_assoc($result)['approved'];
        
        // Pending comments
        $result = mysqli_query($connection, "SELECT COUNT(*) as pending FROM comments WHERE approved = 0");
        $stats['pending_comments'] = mysqli_fetch_assoc($result)['pending'];
        
        // Reported comments
        $result = mysqli_query($connection, "SELECT COUNT(*) as reported FROM comments WHERE report_count > 0");
        $stats['reported_comments'] = mysqli_fetch_assoc($result)['reported'];
        
        // Comments today
        $result = mysqli_query($connection, "SELECT COUNT(*) as today FROM comments WHERE DATE(created_at) = CURDATE()");
        $stats['comments_today'] = mysqli_fetch_assoc($result)['today'];
        
        // Top commenters
        $result = mysqli_query($connection, 
            "SELECT u.username, COUNT(c.id) as comment_count 
             FROM comments c 
             JOIN users u ON c.user_id = u.id 
             WHERE c.approved = 1 
             GROUP BY u.id 
             ORDER BY comment_count DESC 
             LIMIT 5"
        );
        
        $stats['top_commenters'] = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $stats['top_commenters'][] = $row;
        }
        
        return $stats;
    }
    
    /**
     * Moderate comment (approve/reject/delete)
     * @param int $commentId Comment ID
     * @param string $action 'approve', 'reject', or 'delete'
     * @param int $moderatorId Moderator user ID
     * @return bool Success status
     */
    public static function moderateComment($commentId, $action, $moderatorId) {
        global $connection;
        
        switch ($action) {
            case 'approve':
                $query = "UPDATE comments SET approved = 1, moderated_by = ?, moderated_at = NOW() WHERE id = ?";
                break;
                
            case 'reject':
                $query = "UPDATE comments SET approved = 0, moderated_by = ?, moderated_at = NOW() WHERE id = ?";
                break;
                
            case 'delete':
                // First, delete related reactions and reports
                mysqli_query($connection, "DELETE FROM comment_reactions WHERE comment_id = {$commentId}");
                mysqli_query($connection, "DELETE FROM comment_reports WHERE comment_id = {$commentId}");
                
                $query = "DELETE FROM comments WHERE id = ?";
                $stmt = mysqli_prepare($connection, $query);
                mysqli_stmt_bind_param($stmt, 'i', $commentId);
                return mysqli_stmt_execute($stmt);
                
            default:
                return false;
        }
        
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $moderatorId, $commentId);
        return mysqli_stmt_execute($stmt);
    }
    
    /**
     * Get comments requiring moderation
     * @param int $limit Number of comments to retrieve
     * @return array Comments needing moderation
     */
    public static function getCommentsForModeration($limit = 20) {
        global $connection;
        
        $query = "SELECT c.*, u.username, u.email,
                         (SELECT COUNT(*) FROM comment_reports cr WHERE cr.comment_id = c.id) as report_count
                  FROM comments c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.approved = 0 OR c.report_count > 0
                  ORDER BY c.report_count DESC, c.created_at DESC
                  LIMIT ?";
        
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $comments = [];
        while ($comment = mysqli_fetch_assoc($result)) {
            $comment['created_at_formatted'] = self::formatTimestamp($comment['created_at']);
            $comments[] = $comment;
        }
        
        return $comments;
    }
}