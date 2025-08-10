<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'mark_read') {
        $notificationId = intval($_POST['notification_id'] ?? 0);
        db_execute("
            UPDATE notifications 
            SET is_read = 1 
            WHERE id = ? AND user_id = ?
        ", [$notificationId, $_SESSION['user_id']]);
        
    } elseif ($action === 'mark_all_read') {
        db_execute("
            UPDATE notifications 
            SET is_read = 1 
            WHERE user_id = ? AND is_read = 0
        ", [$_SESSION['user_id']]);
    }
}

// Get notifications
$notifications = db_fetch_all("
    SELECT * FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 50
", [$_SESSION['user_id']]);

$unreadCount = db_fetch_column("
    SELECT COUNT(*) FROM notifications 
    WHERE user_id = ? AND is_read = 0
", [$_SESSION['user_id']]);

// Page title
$pageTitle = 'Уведомления' . ($unreadCount > 0 ? " ({$unreadCount})" : '');

// Section 1: Header
ob_start();
?>
<div style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); padding: 60px 20px; color: white; text-align: center;">
    <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 10px;">
        Уведомления 
        <?php if ($unreadCount > 0): ?>
        <span style="background: #dc3545; color: white; border-radius: 20px; 
                     padding: 4px 12px; font-size: 18px; margin-left: 10px;">
            <?= $unreadCount ?>
        </span>
        <?php endif; ?>
    </h1>
    <p style="font-size: 18px; opacity: 0.9;">Все ваши уведомления в одном месте</p>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Notifications
ob_start();
?>
<div style="padding: 40px 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <?php if ($unreadCount > 0): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <p style="color: var(--text-secondary); margin: 0;">
                У вас <?= $unreadCount ?> непрочитанных уведомлений
            </p>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="mark_all_read">
                <button type="submit" 
                        style="background: #28a745; color: white; border: none; padding: 8px 16px; 
                               border-radius: 6px; font-size: 14px; cursor: pointer;">
                    <i class="fas fa-check-double"></i> Отметить все как прочитанные
                </button>
            </form>
        </div>
        <?php endif; ?>
        
        <?php if (empty($notifications)): ?>
        <div style="text-align: center; padding: 60px 20px; background: var(--bg-secondary); 
                    border-radius: 12px; border: 2px dashed var(--border-color);">
            <i class="fas fa-bell" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-secondary); margin-bottom: 10px;">У вас пока нет уведомлений</h3>
            <p style="color: var(--text-secondary);">Уведомления о новых комментариях и событиях будут появляться здесь</p>
        </div>
        <?php else: ?>
        
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($notifications as $notification): ?>
            <div class="notification-item" 
                 style="background: var(--bg-primary); border: 1px solid var(--border-color); 
                        border-radius: 12px; padding: 20px; position: relative; 
                        <?= !$notification['is_read'] ? 'border-left: 4px solid #007bff;' : '' ?>">
                
                <?php if (!$notification['is_read']): ?>
                <div style="position: absolute; top: 15px; right: 15px;">
                    <span style="width: 8px; height: 8px; background: #007bff; border-radius: 50%; display: inline-block;"></span>
                </div>
                <?php endif; ?>
                
                <div style="display: flex; gap: 15px;">
                    <div style="flex-shrink: 0;">
                        <?php
                        $iconColor = '#6c757d';
                        $icon = 'fa-bell';
                        
                        switch($notification['type']) {
                            case 'comment':
                                $icon = 'fa-comment';
                                $iconColor = '#28a745';
                                break;
                            case 'rating':
                                $icon = 'fa-star';
                                $iconColor = '#ffc107';
                                break;
                            case 'favorite':
                                $icon = 'fa-heart';
                                $iconColor = '#dc3545';
                                break;
                            case 'news':
                                $icon = 'fa-newspaper';
                                $iconColor = '#007bff';
                                break;
                            case 'system':
                                $icon = 'fa-cog';
                                $iconColor = '#6f42c1';
                                break;
                        }
                        ?>
                        <i class="fas <?= $icon ?>" style="color: <?= $iconColor ?>; font-size: 20px; margin-top: 2px;"></i>
                    </div>
                    
                    <div style="flex: 1;">
                        <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">
                            <?= htmlspecialchars($notification['title']) ?>
                        </h4>
                        
                        <?php if ($notification['message']): ?>
                        <p style="margin: 0 0 12px 0; color: var(--text-secondary); line-height: 1.5;">
                            <?= htmlspecialchars($notification['message']) ?>
                        </p>
                        <?php endif; ?>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 14px; color: var(--text-secondary);">
                                <i class="fas fa-clock"></i> 
                                <?= date('d.m.Y H:i', strtotime($notification['created_at'])) ?>
                            </span>
                            
                            <div style="display: flex; gap: 10px;">
                                <?php if ($notification['link']): ?>
                                <a href="<?= htmlspecialchars($notification['link']) ?>" 
                                   style="color: #007bff; text-decoration: none; font-size: 14px;">
                                    <i class="fas fa-external-link-alt"></i> Перейти
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!$notification['is_read']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="notification_id" value="<?= $notification['id'] ?>">
                                    <button type="submit" 
                                            style="background: transparent; color: #28a745; border: none; 
                                                   cursor: pointer; font-size: 14px;">
                                        <i class="fas fa-check"></i> Прочитано
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($notifications) >= 50): ?>
        <div style="text-align: center; margin-top: 30px;">
            <p style="color: var(--text-secondary);">Показаны последние 50 уведомлений</p>
        </div>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>
</div>

<style>
.notification-item:hover {
    box-shadow: 0 4px 12px var(--shadow);
    transition: box-shadow 0.3s ease;
}
</style>

<script>
// Auto-refresh notifications every 30 seconds
setInterval(function() {
    // Check for new notifications via AJAX
    fetch('/api/notifications/check')
        .then(response => response.json())
        .then(data => {
            if (data.new_count > 0) {
                // Update page title with notification count
                document.title = `Уведомления (${data.total_unread}) - 11klassniki.ru`;
                
                // Show notification badge
                updateNotificationBadge(data.total_unread);
            }
        })
        .catch(error => console.log('Notification check error:', error));
}, 30000);

function updateNotificationBadge(count) {
    // Update notification badge in header if it exists
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
}
</script>
<?php
$greyContent2 = ob_get_clean();

// Include template
include 'real_template_local.php';
?>