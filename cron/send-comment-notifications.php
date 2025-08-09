<?php
/**
 * Send Comment Reply Notifications
 * Run this script via cron every 5-10 minutes
 * Example cron: */10 * * * * /usr/bin/php /path/to/send-comment-notifications.php
 */

// Prevent web access
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from command line');
}

// Database connection
require_once dirname(__DIR__) . '/database/db_connections.php';

// Email configuration
$from_email = 'noreply@11klassniki.ru';
$from_name = '11 Классники';
$site_url = 'https://11klassniki.ru';

// Get pending notifications
$query = "SELECT n.*, c.comment_text as reply_text, c.author_of_comment as reply_author,
          pc.comment_text as parent_text, pc.author_of_comment as parent_author,
          pc.entity_type, pc.entity_id
          FROM comment_notifications n
          JOIN comments c ON n.comment_id = c.id
          JOIN comments pc ON n.parent_comment_id = pc.id
          WHERE n.is_sent = 0 AND n.email IS NOT NULL
          ORDER BY n.created_at ASC
          LIMIT 50";

$result = $connection->query($query);
$sent_count = 0;
$error_count = 0;

while ($notification = $result->fetch_assoc()) {
    try {
        // Build the email
        $subject = "Новый ответ на ваш комментарий";
        
        // Get entity link
        $entity_link = '';
        switch ($notification['entity_type']) {
            case 'posts':
                $entity_link = $site_url . '/post/' . $notification['entity_id'];
                break;
            case 'spo':
                $entity_link = $site_url . '/spo/' . $notification['entity_id'];
                break;
            case 'vpo':
                $entity_link = $site_url . '/vpo/' . $notification['entity_id'];
                break;
            case 'school':
                $entity_link = $site_url . '/school/' . $notification['entity_id'];
                break;
        }
        
        // Build HTML email
        $html_body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($subject) . '</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #007bff 0%, #6c63ff 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .comment-box {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .comment-author {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .comment-text {
            color: #555;
        }
        .reply-box {
            background: #e8f4fd;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .button:hover {
            background: #0056b3;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .unsubscribe {
            color: #999;
            font-size: 12px;
            margin-top: 10px;
        }
        .unsubscribe a {
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Новый ответ на ваш комментарий</h1>
        </div>
        
        <div class="content">
            <p>Здравствуйте, ' . htmlspecialchars($notification['parent_author']) . '!</p>
            
            <p>Пользователь <strong>' . htmlspecialchars($notification['reply_author']) . '</strong> ответил на ваш комментарий.</p>
            
            <div class="comment-box">
                <div class="comment-author">Ваш комментарий:</div>
                <div class="comment-text">' . nl2br(htmlspecialchars($notification['parent_text'])) . '</div>
            </div>
            
            <div class="reply-box">
                <div class="comment-author">Ответ от ' . htmlspecialchars($notification['reply_author']) . ':</div>
                <div class="comment-text">' . nl2br(htmlspecialchars($notification['reply_text'])) . '</div>
            </div>
            
            <div style="text-align: center;">
                <a href="' . $entity_link . '#comment-' . $notification['comment_id'] . '" class="button">Посмотреть обсуждение</a>
            </div>
        </div>
        
        <div class="footer">
            <p>С уважением,<br>Команда 11 Классники</p>
            <div class="unsubscribe">
                Вы получили это письмо, потому что оставили комментарий на сайте 11klassniki.ru
            </div>
        </div>
    </div>
</body>
</html>';
        
        // Plain text version
        $text_body = "Здравствуйте, {$notification['parent_author']}!\n\n";
        $text_body .= "Пользователь {$notification['reply_author']} ответил на ваш комментарий.\n\n";
        $text_body .= "Ваш комментарий:\n{$notification['parent_text']}\n\n";
        $text_body .= "Ответ:\n{$notification['reply_text']}\n\n";
        $text_body .= "Посмотреть обсуждение: {$entity_link}#comment-{$notification['comment_id']}\n\n";
        $text_body .= "С уважением,\nКоманда 11 Классники";
        
        // Email headers
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $from_name . ' <' . $from_email . '>',
            'Reply-To: ' . $from_email,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        // Send email
        if (mail($notification['email'], $subject, $html_body, implode("\r\n", $headers))) {
            // Mark as sent
            $stmt = $connection->prepare("UPDATE comment_notifications SET is_sent = 1, sent_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $notification['id']);
            $stmt->execute();
            $sent_count++;
            
            echo "[" . date('Y-m-d H:i:s') . "] Sent notification to {$notification['email']} for comment {$notification['comment_id']}\n";
        } else {
            $error_count++;
            echo "[" . date('Y-m-d H:i:s') . "] ERROR: Failed to send to {$notification['email']} for comment {$notification['comment_id']}\n";
        }
        
        // Rate limiting - wait 1 second between emails
        sleep(1);
        
    } catch (Exception $e) {
        $error_count++;
        echo "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Completed: Sent {$sent_count} notifications, {$error_count} errors\n";
?>