<?php
// Account comments page - migrated to use real_template.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$userId = $_SESSION['user_id'];
$occupation = $_SESSION['occupation'] ?? '';

// Fetch user's comments
$query = "SELECT c.*, n.title_news, n.url_slug as news_url, p.title_post, p.url_post 
          FROM comments c
          LEFT JOIN news n ON c.news_id = n.id AND c.type = 'news'
          LEFT JOIN posts p ON c.post_id = p.id_post AND c.type = 'post'
          WHERE c.user_id = ?
          ORDER BY c.created_at DESC
          LIMIT 50";

$stmt = $connection->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$comments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Мои комментарии', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Все ваши комментарии на сайте'
]);
$greyContent1 = ob_get_clean();

// Section 2: Account Navigation
ob_start();
?>
<div style="padding: 20px;">
    <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
        <a href="/account" class="account-nav-item">Обзор</a>
        <a href="/account/edit" class="account-nav-item">Редактировать профиль</a>
        <a href="/account/password-change" class="account-nav-item">Изменить пароль</a>
        <a href="/account/comments" class="account-nav-item active">Мои комментарии</a>
        <?php if ($occupation === 'admin'): ?>
        <a href="/dashboard" class="account-nav-item">Панель управления</a>
        <?php endif; ?>
        <a href="/logout" class="account-nav-item" style="color: #dc3545;">Выйти</a>
    </div>
</div>

<style>
.account-nav-item {
    padding: 10px 20px;
    background: var(--surface, #f8f9fa);
    color: var(--text-primary, #333);
    text-decoration: none;
    border-radius: 25px;
    transition: all 0.3s;
    font-weight: 500;
}

.account-nav-item:hover {
    background: #28a745;
    color: white;
    transform: translateY(-2px);
}

.account-nav-item.active {
    background: #28a745;
    color: white;
}

[data-theme="dark"] .account-nav-item {
    background: var(--surface-dark, #2d3748);
    color: var(--text-primary, #e4e6eb);
}
</style>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Comments List
ob_start();
?>
<div style="max-width: 900px; margin: 0 auto; padding: 20px;">
    <?php if (empty($comments)): ?>
        <div style="background: var(--surface, #ffffff); padding: 60px; border-radius: 12px; 
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
            <i class="fas fa-comments" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-primary, #333); margin-bottom: 10px;">Комментариев пока нет</h3>
            <p style="color: var(--text-secondary, #666);">
                Вы еще не оставляли комментарии на сайте.
            </p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($comments as $comment): ?>
                <div style="background: var(--surface, #ffffff); padding: 25px; border-radius: 12px; 
                            box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    
                    <div style="margin-bottom: 15px;">
                        <?php if ($comment['type'] === 'news' && !empty($comment['title_news'])): ?>
                            <a href="/news/<?= htmlspecialchars($comment['news_url']) ?>" 
                               style="color: #28a745; text-decoration: none; font-weight: 500;">
                                <i class="fas fa-newspaper"></i> <?= htmlspecialchars($comment['title_news']) ?>
                            </a>
                        <?php elseif ($comment['type'] === 'post' && !empty($comment['title_post'])): ?>
                            <a href="/post/<?= htmlspecialchars($comment['url_post']) ?>" 
                               style="color: #28a745; text-decoration: none; font-weight: 500;">
                                <i class="fas fa-file-alt"></i> <?= htmlspecialchars($comment['title_post']) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div style="color: var(--text-primary, #333); line-height: 1.6; margin-bottom: 15px;">
                        <?= nl2br(htmlspecialchars($comment['comment_text'])) ?>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <small style="color: var(--text-secondary, #666);">
                            <i class="far fa-clock"></i> 
                            <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?>
                        </small>
                        
                        <?php if ($comment['status'] === 'approved'): ?>
                            <span style="color: #28a745; font-size: 14px;">
                                <i class="fas fa-check-circle"></i> Одобрен
                            </span>
                        <?php elseif ($comment['status'] === 'pending'): ?>
                            <span style="color: #ffc107; font-size: 14px;">
                                <i class="fas fa-clock"></i> На модерации
                            </span>
                        <?php else: ?>
                            <span style="color: #dc3545; font-size: 14px;">
                                <i class="fas fa-times-circle"></i> Отклонен
                            </span>
                        <?php endif; ?>
                    </div>
                    
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
[data-theme="dark"] div[style*="background: var(--surface, #ffffff)"] {
    background: var(--surface-dark, #2d3748) !important;
}

[data-theme="dark"] h3 {
    color: var(--text-primary, #e4e6eb) !important;
}

[data-theme="dark"] p,
[data-theme="dark"] small {
    color: var(--text-secondary, #b0b3b8) !important;
}

[data-theme="dark"] div[style*="color: var(--text-primary, #333)"] {
    color: var(--text-primary, #e4e6eb) !important;
}
</style>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty pagination
$greyContent6 = '';

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Мои комментарии - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>