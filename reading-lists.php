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

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_list') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $isPublic = isset($_POST['is_public']) ? 1 : 0;
        
        if ($name) {
            $listId = db_insert_id("
                INSERT INTO reading_lists (user_id, name, description, is_public)
                VALUES (?, ?, ?, ?)
            ", [$_SESSION['user_id'], $name, $description, $isPublic]);
            
            if ($listId) {
                $message = 'Список успешно создан';
            } else {
                $error = 'Ошибка при создании списка';
            }
        } else {
            $error = 'Введите название списка';
        }
    } elseif ($action === 'delete_list') {
        $listId = intval($_POST['list_id'] ?? 0);
        
        // Verify ownership
        $list = db_fetch_one("
            SELECT id FROM reading_lists 
            WHERE id = ? AND user_id = ?
        ", [$listId, $_SESSION['user_id']]);
        
        if ($list) {
            db_execute("DELETE FROM reading_lists WHERE id = ?", [$listId]);
            $message = 'Список удален';
        } else {
            $error = 'Список не найден';
        }
    }
}

// Get user's reading lists
$myLists = db_fetch_all("
    SELECT rl.*, 
           COUNT(rli.id) as item_count,
           SUM(CASE WHEN rli.is_read = 1 THEN 1 ELSE 0 END) as read_count
    FROM reading_lists rl
    LEFT JOIN reading_list_items rli ON rl.id = rli.list_id
    WHERE rl.user_id = ?
    GROUP BY rl.id
    ORDER BY rl.created_at DESC
", [$_SESSION['user_id']]);

// Get public lists from other users
$publicLists = db_fetch_all("
    SELECT rl.*, u.name as author_name,
           COUNT(rli.id) as item_count,
           SUM(CASE WHEN rli.is_read = 1 THEN 1 ELSE 0 END) as read_count
    FROM reading_lists rl
    JOIN users u ON rl.user_id = u.id
    LEFT JOIN reading_list_items rli ON rl.id = rli.list_id
    WHERE rl.is_public = 1 AND rl.user_id != ?
    GROUP BY rl.id
    ORDER BY item_count DESC, rl.created_at DESC
    LIMIT 10
", [$_SESSION['user_id']]);

// Page title
$pageTitle = 'Списки для чтения';

// Section 1: Header
ob_start();
?>
<div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 60px 20px; color: white; text-align: center;">
    <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 10px;">Списки для чтения</h1>
    <p style="font-size: 18px; opacity: 0.9;">Организуйте свои материалы для изучения</p>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: My Lists
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
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 28px; margin: 0;">Мои списки</h2>
            <button onclick="showCreateForm()" 
                    style="background: #28a745; color: white; border: none; padding: 12px 24px; 
                           border-radius: 8px; font-size: 16px; cursor: pointer; font-weight: 600;">
                <i class="fas fa-plus"></i> Создать список
            </button>
        </div>
        
        <!-- Create list form (hidden by default) -->
        <div id="createForm" style="display: none; background: var(--bg-primary); border: 1px solid var(--border-color); 
                                    border-radius: 12px; padding: 30px; margin-bottom: 30px;">
            <h3 style="margin-top: 0;">Создать новый список</h3>
            <form method="POST">
                <input type="hidden" name="action" value="create_list">
                <div style="display: grid; gap: 20px;">
                    <div>
                        <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600;">
                            Название списка
                        </label>
                        <input type="text" id="name" name="name" required
                               style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                                      border-radius: 8px; font-size: 16px;">
                    </div>
                    <div>
                        <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600;">
                            Описание (необязательно)
                        </label>
                        <textarea id="description" name="description" rows="3"
                                  style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                                         border-radius: 8px; font-size: 16px; resize: vertical;"></textarea>
                    </div>
                    <div>
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="is_public">
                            <span>Сделать список публичным (другие пользователи смогут его видеть)</span>
                        </label>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" 
                                style="background: #28a745; color: white; border: none; padding: 10px 20px; 
                                       border-radius: 6px; cursor: pointer;">
                            <i class="fas fa-save"></i> Создать
                        </button>
                        <button type="button" onclick="hideCreateForm()" 
                                style="background: var(--bg-secondary); color: var(--text-primary); 
                                       border: 1px solid var(--border-color); padding: 10px 20px; 
                                       border-radius: 6px; cursor: pointer;">
                            Отмена
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- My Lists Grid -->
        <?php if (empty($myLists)): ?>
        <div style="text-align: center; padding: 60px 20px; background: var(--bg-secondary); 
                    border-radius: 12px; border: 2px dashed var(--border-color);">
            <i class="fas fa-list" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-secondary); margin-bottom: 10px;">У вас пока нет списков</h3>
            <p style="color: var(--text-secondary);">Создайте свой первый список для чтения</p>
            <button onclick="showCreateForm()" 
                    style="background: #28a745; color: white; border: none; padding: 12px 24px; 
                           border-radius: 8px; font-size: 16px; cursor: pointer; margin-top: 20px;">
                <i class="fas fa-plus"></i> Создать список
            </button>
        </div>
        <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php foreach ($myLists as $list): ?>
            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); 
                        border-radius: 12px; padding: 25px; position: relative;">
                <div style="position: absolute; top: 15px; right: 15px;">
                    <div style="position: relative;">
                        <button onclick="toggleListMenu(<?= $list['id'] ?>)" 
                                style="background: transparent; border: none; cursor: pointer; 
                                       color: var(--text-secondary); font-size: 18px;">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div id="listMenu<?= $list['id'] ?>" 
                             style="display: none; position: absolute; right: 0; top: 100%; 
                                    background: var(--bg-primary); border: 1px solid var(--border-color); 
                                    border-radius: 6px; box-shadow: 0 2px 8px var(--shadow); min-width: 120px; z-index: 10;">
                            <a href="/reading-list/<?= $list['id'] ?>" 
                               style="display: block; padding: 8px 12px; color: var(--text-primary); 
                                      text-decoration: none; border-bottom: 1px solid var(--border-color);">
                                <i class="fas fa-eye"></i> Просмотр
                            </a>
                            <button onclick="deleteList(<?= $list['id'] ?>)" 
                                    style="width: 100%; text-align: left; background: transparent; 
                                           border: none; padding: 8px 12px; color: #dc3545; cursor: pointer;">
                                <i class="fas fa-trash"></i> Удалить
                            </button>
                        </div>
                    </div>
                </div>
                
                <h3 style="margin: 0 0 10px 0; font-size: 20px; padding-right: 40px;">
                    <a href="/reading-list/<?= $list['id'] ?>" style="color: var(--link-color); text-decoration: none;">
                        <?= htmlspecialchars($list['name']) ?>
                    </a>
                    <?php if ($list['is_public']): ?>
                    <i class="fas fa-globe" style="font-size: 14px; color: #28a745; margin-left: 8px;" title="Публичный список"></i>
                    <?php endif; ?>
                </h3>
                
                <?php if ($list['description']): ?>
                <p style="color: var(--text-secondary); margin-bottom: 15px; font-size: 14px;">
                    <?= htmlspecialchars($list['description']) ?>
                </p>
                <?php endif; ?>
                
                <div style="display: flex; justify-content: space-between; align-items: center; 
                            padding-top: 15px; border-top: 1px solid var(--border-color); font-size: 14px;">
                    <span style="color: var(--text-secondary);">
                        <i class="fas fa-bookmark"></i> <?= $list['item_count'] ?> материалов
                    </span>
                    <?php if ($list['item_count'] > 0): ?>
                    <span style="color: #28a745;">
                        <i class="fas fa-check"></i> <?= $list['read_count'] ?> прочитано
                    </span>
                    <?php endif; ?>
                </div>
                
                <div style="margin-top: 15px;">
                    <a href="/reading-list/<?= $list['id'] ?>" 
                       style="display: inline-block; background: #007bff; color: white; 
                              text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 14px;">
                        Открыть список
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function showCreateForm() {
    document.getElementById('createForm').style.display = 'block';
    document.getElementById('name').focus();
}

function hideCreateForm() {
    document.getElementById('createForm').style.display = 'none';
}

function toggleListMenu(listId) {
    const menu = document.getElementById('listMenu' + listId);
    const allMenus = document.querySelectorAll('[id^="listMenu"]');
    
    // Close all other menus
    allMenus.forEach(m => {
        if (m !== menu) m.style.display = 'none';
    });
    
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function deleteList(listId) {
    if (confirm('Вы уверены, что хотите удалить этот список? Все материалы в нем будут потеряны.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_list">
            <input type="hidden" name="list_id" value="${listId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleListMenu"]')) {
        document.querySelectorAll('[id^="listMenu"]').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});
</script>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Public Lists
if (!empty($publicLists)):
ob_start();
?>
<div style="padding: 40px 20px; background: var(--bg-secondary);">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h2 style="font-size: 28px; margin-bottom: 30px;">Популярные публичные списки</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php foreach ($publicLists as $list): ?>
            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); 
                        border-radius: 12px; padding: 25px;">
                <h3 style="margin: 0 0 10px 0; font-size: 20px;">
                    <a href="/reading-list/<?= $list['id'] ?>" style="color: var(--link-color); text-decoration: none;">
                        <?= htmlspecialchars($list['name']) ?>
                    </a>
                    <i class="fas fa-globe" style="font-size: 14px; color: #28a745; margin-left: 8px;"></i>
                </h3>
                
                <p style="color: var(--text-secondary); margin-bottom: 15px; font-size: 14px;">
                    Автор: <strong><?= htmlspecialchars($list['author_name']) ?></strong>
                </p>
                
                <?php if ($list['description']): ?>
                <p style="color: var(--text-secondary); margin-bottom: 15px; font-size: 14px;">
                    <?= htmlspecialchars($list['description']) ?>
                </p>
                <?php endif; ?>
                
                <div style="display: flex; justify-content: space-between; align-items: center; 
                            padding-top: 15px; border-top: 1px solid var(--border-color); font-size: 14px;">
                    <span style="color: var(--text-secondary);">
                        <i class="fas fa-bookmark"></i> <?= $list['item_count'] ?> материалов
                    </span>
                </div>
                
                <div style="margin-top: 15px;">
                    <a href="/reading-list/<?= $list['id'] ?>" 
                       style="display: inline-block; background: #28a745; color: white; 
                              text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 14px;">
                        Посмотреть список
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();
endif;

// Include template
include 'template.php';
?>