<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get list ID from URL
$listId = intval($_GET['id'] ?? 0);

if (!$listId) {
    header('HTTP/1.0 404 Not Found');
    include '404_modern.php';
    exit;
}

// Get list details
$list = db_fetch_one("
    SELECT rl.*, u.name as author_name
    FROM reading_lists rl
    JOIN users u ON rl.user_id = u.id
    WHERE rl.id = ?
", [$listId]);

if (!$list) {
    header('HTTP/1.0 404 Not Found');
    include '404_modern.php';
    exit;
}

// Check access permissions
$canEdit = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $list['user_id'];
$canView = $list['is_public'] || $canEdit;

if (!$canView) {
    header('Location: /login');
    exit;
}

$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canEdit) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'toggle_read') {
        $itemId = intval($_POST['item_id'] ?? 0);
        $isRead = intval($_POST['is_read'] ?? 0);
        
        db_execute("
            UPDATE reading_list_items 
            SET is_read = ?, read_at = ?
            WHERE id = ? AND list_id IN (
                SELECT id FROM reading_lists WHERE user_id = ?
            )
        ", [$isRead, $isRead ? date('Y-m-d H:i:s') : null, $itemId, $_SESSION['user_id']]);
        
    } elseif ($action === 'remove_item') {
        $itemId = intval($_POST['item_id'] ?? 0);
        
        db_execute("
            DELETE FROM reading_list_items 
            WHERE id = ? AND list_id IN (
                SELECT id FROM reading_lists WHERE user_id = ?
            )
        ", [$itemId, $_SESSION['user_id']]);
        
        $message = 'Материал удален из списка';
    } elseif ($action === 'add_notes') {
        $itemId = intval($_POST['item_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');
        
        db_execute("
            UPDATE reading_list_items 
            SET notes = ?
            WHERE id = ? AND list_id IN (
                SELECT id FROM reading_lists WHERE user_id = ?
            )
        ", [$notes, $itemId, $_SESSION['user_id']]);
        
        $message = 'Заметки сохранены';
    }
}

// Get list items with content details
$items = db_fetch_all("
    SELECT rli.*,
        CASE 
            WHEN rli.item_type = 'news' THEN n.title_news
            WHEN rli.item_type = 'post' THEN p.title_post
            WHEN rli.item_type = 'vpo' THEN v.name_vpo
            WHEN rli.item_type = 'spo' THEN s.name_spo
            WHEN rli.item_type = 'school' THEN sc.name_school
        END as title,
        CASE 
            WHEN rli.item_type = 'news' THEN n.url_news
            WHEN rli.item_type = 'post' THEN p.url_slug
            WHEN rli.item_type = 'vpo' THEN v.url_slug
            WHEN rli.item_type = 'spo' THEN s.url_slug
            WHEN rli.item_type = 'school' THEN sc.url_slug
        END as url,
        CASE 
            WHEN rli.item_type = 'news' THEN n.text_news
            WHEN rli.item_type = 'post' THEN p.text_post
            WHEN rli.item_type = 'vpo' THEN v.description
            WHEN rli.item_type = 'spo' THEN s.description
            WHEN rli.item_type = 'school' THEN sc.description
        END as description
    FROM reading_list_items rli
    LEFT JOIN news n ON rli.item_type = 'news' AND rli.item_id = n.id_news
    LEFT JOIN posts p ON rli.item_type = 'post' AND rli.item_id = p.id
    LEFT JOIN vpo v ON rli.item_type = 'vpo' AND rli.item_id = v.id_university
    LEFT JOIN spo s ON rli.item_type = 'spo' AND rli.item_id = s.id_college
    LEFT JOIN schools sc ON rli.item_type = 'school' AND rli.item_id = sc.id_school
    WHERE rli.list_id = ?
    ORDER BY rli.added_at DESC
", [$listId]);

// Page title
$pageTitle = htmlspecialchars($list['name']) . ' - Список для чтения';

// Section 1: Header
ob_start();
?>
<div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 60px 20px; color: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/breadcrumbs.php';
        render_breadcrumbs([
            ['title' => 'Списки для чтения', 'url' => '/reading-lists'],
            ['title' => $list['name'], 'url' => null]
        ]);
        ?>
        
        <div style="text-align: center; margin-top: 20px;">
            <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 10px;">
                <?= htmlspecialchars($list['name']) ?>
                <?php if ($list['is_public']): ?>
                <i class="fas fa-globe" style="font-size: 24px; margin-left: 10px; opacity: 0.8;"></i>
                <?php endif; ?>
            </h1>
            
            <?php if ($list['description']): ?>
            <p style="font-size: 18px; opacity: 0.9; margin-bottom: 15px;">
                <?= htmlspecialchars($list['description']) ?>
            </p>
            <?php endif; ?>
            
            <div style="display: flex; justify-content: center; gap: 30px; font-size: 16px; opacity: 0.9;">
                <span>
                    <i class="fas fa-user"></i> <?= htmlspecialchars($list['author_name']) ?>
                </span>
                <span>
                    <i class="fas fa-bookmark"></i> <?= count($items) ?> материалов
                </span>
                <span>
                    <i class="fas fa-check"></i> <?= count(array_filter($items, fn($item) => $item['is_read'])) ?> прочитано
                </span>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: List items
ob_start();
?>
<div style="padding: 40px 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <?php if ($message): ?>
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; 
                    padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; 
                    padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <?php if (empty($items)): ?>
        <div style="text-align: center; padding: 60px 20px; background: var(--bg-secondary); 
                    border-radius: 12px; border: 2px dashed var(--border-color);">
            <i class="fas fa-bookmark" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-secondary); margin-bottom: 10px;">Список пока пуст</h3>
            <p style="color: var(--text-secondary);">
                <?php if ($canEdit): ?>
                Добавьте материалы в этот список, используя кнопку "Добавить в список" на страницах статей и новостей
                <?php else: ?>
                В этом списке пока нет материалов
                <?php endif; ?>
            </p>
        </div>
        <?php else: ?>
        
        <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 30px;">
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <button onclick="filterItems('all')" class="filter-btn active" data-filter="all"
                        style="padding: 8px 16px; border: 1px solid var(--border-color); 
                               background: #007bff; color: white; border-radius: 20px; 
                               cursor: pointer; font-size: 14px;">
                    Все (<?= count($items) ?>)
                </button>
                <button onclick="filterItems('unread')" class="filter-btn" data-filter="unread"
                        style="padding: 8px 16px; border: 1px solid var(--border-color); 
                               background: var(--bg-primary); color: var(--text-primary); border-radius: 20px; 
                               cursor: pointer; font-size: 14px;">
                    Не прочитано (<?= count(array_filter($items, fn($item) => !$item['is_read'])) ?>)
                </button>
                <button onclick="filterItems('read')" class="filter-btn" data-filter="read"
                        style="padding: 8px 16px; border: 1px solid var(--border-color); 
                               background: var(--bg-primary); color: var(--text-primary); border-radius: 20px; 
                               cursor: pointer; font-size: 14px;">
                    Прочитано (<?= count(array_filter($items, fn($item) => $item['is_read'])) ?>)
                </button>
            </div>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($items as $item): ?>
            <div class="list-item" data-status="<?= $item['is_read'] ? 'read' : 'unread' ?>"
                 style="background: var(--bg-primary); border: 1px solid var(--border-color); 
                        border-radius: 12px; padding: 25px; <?= $item['is_read'] ? 'opacity: 0.7;' : '' ?>">
                
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: start; gap: 15px; margin-bottom: 15px;">
                            <?php
                            $icon = 'fa-file';
                            $color = '#6c757d';
                            $link = '#';
                            
                            switch($item['item_type']) {
                                case 'news':
                                    $icon = 'fa-newspaper';
                                    $color = '#007bff';
                                    $link = '/news/' . $item['url'];
                                    break;
                                case 'post':
                                    $icon = 'fa-book';
                                    $color = '#28a745';
                                    $link = '/post/' . $item['url'];
                                    break;
                                case 'vpo':
                                    $icon = 'fa-university';
                                    $color = '#dc3545';
                                    $link = '/vpo/' . $item['url'];
                                    break;
                                case 'spo':
                                    $icon = 'fa-school';
                                    $color = '#ffc107';
                                    $link = '/spo/' . $item['url'];
                                    break;
                                case 'school':
                                    $icon = 'fa-graduation-cap';
                                    $color = '#17a2b8';
                                    $link = '/school/' . $item['url'];
                                    break;
                            }
                            ?>
                            <i class="fas <?= $icon ?>" style="color: <?= $color ?>; font-size: 20px; margin-top: 2px;"></i>
                            
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 10px 0; font-size: 20px; <?= $item['is_read'] ? 'text-decoration: line-through;' : '' ?>">
                                    <a href="<?= $link ?>" target="_blank" style="color: var(--link-color); text-decoration: none;">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </a>
                                </h3>
                                
                                <?php if ($item['description']): ?>
                                <p style="color: var(--text-secondary); margin: 0 0 15px 0; line-height: 1.6;">
                                    <?= htmlspecialchars(mb_substr(strip_tags($item['description']), 0, 200)) ?>...
                                </p>
                                <?php endif; ?>
                                
                                <div style="display: flex; gap: 20px; font-size: 14px; color: var(--text-secondary); margin-bottom: 15px;">
                                    <span>
                                        <i class="fas fa-calendar-plus"></i> 
                                        Добавлено: <?= date('d.m.Y H:i', strtotime($item['added_at'])) ?>
                                    </span>
                                    <?php if ($item['is_read'] && $item['read_at']): ?>
                                    <span>
                                        <i class="fas fa-check"></i> 
                                        Прочитано: <?= date('d.m.Y H:i', strtotime($item['read_at'])) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($item['notes']): ?>
                                <div style="background: var(--bg-secondary); padding: 15px; border-radius: 8px; 
                                            border-left: 3px solid #007bff; margin-bottom: 15px;">
                                    <strong style="color: var(--text-primary);">Заметки:</strong>
                                    <p style="margin: 5px 0 0 0; color: var(--text-secondary);">
                                        <?= nl2br(htmlspecialchars($item['notes'])) ?>
                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($canEdit): ?>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="toggle_read">
                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                <input type="hidden" name="is_read" value="<?= $item['is_read'] ? 0 : 1 ?>">
                                <button type="submit" 
                                        style="background: <?= $item['is_read'] ? '#6c757d' : '#28a745' ?>; color: white; 
                                               border: none; padding: 6px 12px; border-radius: 6px; 
                                               font-size: 14px; cursor: pointer;">
                                    <i class="fas fa-<?= $item['is_read'] ? 'undo' : 'check' ?>"></i>
                                    <?= $item['is_read'] ? 'Не прочитано' : 'Прочитано' ?>
                                </button>
                            </form>
                            
                            <button onclick="showNotesForm(<?= $item['id'] ?>)" 
                                    style="background: #007bff; color: white; border: none; padding: 6px 12px; 
                                           border-radius: 6px; font-size: 14px; cursor: pointer;">
                                <i class="fas fa-sticky-note"></i> Заметки
                            </button>
                            
                            <form method="POST" style="display: inline;" 
                                  onsubmit="return confirm('Удалить этот материал из списка?')">
                                <input type="hidden" name="action" value="remove_item">
                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                <button type="submit" 
                                        style="background: #dc3545; color: white; border: none; padding: 6px 12px; 
                                               border-radius: 6px; font-size: 14px; cursor: pointer;">
                                    <i class="fas fa-times"></i> Удалить
                                </button>
                            </form>
                        </div>
                        
                        <!-- Notes form (hidden by default) -->
                        <div id="notesForm<?= $item['id'] ?>" style="display: none; margin-top: 15px; 
                                                                      padding: 15px; background: var(--bg-secondary); 
                                                                      border-radius: 8px;">
                            <form method="POST">
                                <input type="hidden" name="action" value="add_notes">
                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Заметки:</label>
                                <textarea name="notes" rows="3" 
                                          style="width: 100%; padding: 8px; border: 1px solid var(--border-color); 
                                                 border-radius: 4px; resize: vertical;"><?= htmlspecialchars($item['notes']) ?></textarea>
                                <div style="margin-top: 10px;">
                                    <button type="submit" 
                                            style="background: #28a745; color: white; border: none; padding: 6px 12px; 
                                                   border-radius: 4px; font-size: 14px; cursor: pointer;">
                                        Сохранить
                                    </button>
                                    <button type="button" onclick="hideNotesForm(<?= $item['id'] ?>)" 
                                            style="background: var(--bg-secondary); color: var(--text-primary); 
                                                   border: 1px solid var(--border-color); padding: 6px 12px; 
                                                   border-radius: 4px; font-size: 14px; cursor: pointer; margin-left: 8px;">
                                        Отмена
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function filterItems(filter) {
    const items = document.querySelectorAll('.list-item');
    const buttons = document.querySelectorAll('.filter-btn');
    
    // Update button states
    buttons.forEach(btn => {
        if (btn.dataset.filter === filter) {
            btn.style.background = '#007bff';
            btn.style.color = 'white';
        } else {
            btn.style.background = 'var(--bg-primary)';
            btn.style.color = 'var(--text-primary)';
        }
    });
    
    // Filter items
    items.forEach(item => {
        const status = item.dataset.status;
        if (filter === 'all' || filter === status) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function showNotesForm(itemId) {
    document.getElementById('notesForm' + itemId).style.display = 'block';
}

function hideNotesForm(itemId) {
    document.getElementById('notesForm' + itemId).style.display = 'none';
}
</script>
<?php
$greyContent2 = ob_get_clean();

// Include template
include 'template.php';
?>