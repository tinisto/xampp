<?php
// Account page - migrated to use real_template.php

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
$userEmail = $_SESSION['email'] ?? '';
$firstName = $_SESSION['first_name'] ?? '';
$lastName = $_SESSION['last_name'] ?? '';
$occupation = $_SESSION['occupation'] ?? '';

// Fetch user stats
$commentsCount = 0;
$newsCount = 0;
$postsCount = 0;

// Get comments count
$stmt = $connection->prepare("SELECT COUNT(*) as count FROM comments WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$commentsCount = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

// Get news count
$stmt = $connection->prepare("SELECT COUNT(*) as count FROM news WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$newsCount = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

// Get posts count
$stmt = $connection->prepare("SELECT COUNT(*) as count FROM posts WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$postsCount = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Личный кабинет', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Добро пожаловать, ' . htmlspecialchars($firstName)
]);
$greyContent1 = ob_get_clean();

// Section 2: Account Navigation
ob_start();
?>
<div style="padding: 20px;">
    <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
        <a href="/account" class="account-nav-item active">Обзор</a>
        <a href="/account/edit" class="account-nav-item">Редактировать профиль</a>
        <a href="/account/password-change" class="account-nav-item">Изменить пароль</a>
        <a href="/account/comments" class="account-nav-item">Мои комментарии</a>
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

// Section 4: User Info
ob_start();
?>
<div style="padding: 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="background: var(--surface, #ffffff); padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px; color: var(--text-primary, #333);">Информация о профиле</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div>
                    <label style="color: #666; font-size: 14px;">Имя</label>
                    <p style="color: var(--text-primary, #333); font-size: 16px; margin: 5px 0;"><?= htmlspecialchars($firstName . ' ' . $lastName) ?></p>
                </div>
                <div>
                    <label style="color: #666; font-size: 14px;">Email</label>
                    <p style="color: var(--text-primary, #333); font-size: 16px; margin: 5px 0;"><?= htmlspecialchars($userEmail) ?></p>
                </div>
                <div>
                    <label style="color: #666; font-size: 14px;">Статус</label>
                    <p style="color: var(--text-primary, #333); font-size: 16px; margin: 5px 0;">
                        <?= $occupation === 'admin' ? 'Администратор' : 'Пользователь' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent4 = ob_get_clean();

// Section 5: User Statistics
ob_start();
?>
<div style="padding: 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h3 style="margin-bottom: 20px; text-align: center; color: var(--text-primary, #333);">Ваша активность</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div style="background: var(--surface, #ffffff); padding: 30px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <i class="fas fa-comments fa-3x" style="color: #28a745; margin-bottom: 15px;"></i>
                <h4 style="color: var(--text-primary, #333); margin: 10px 0;"><?= $commentsCount ?></h4>
                <p style="color: #666;">Комментариев</p>
            </div>
            <div style="background: var(--surface, #ffffff); padding: 30px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <i class="fas fa-newspaper fa-3x" style="color: #17a2b8; margin-bottom: 15px;"></i>
                <h4 style="color: var(--text-primary, #333); margin: 10px 0;"><?= $newsCount ?></h4>
                <p style="color: #666;">Новостей</p>
            </div>
            <div style="background: var(--surface, #ffffff); padding: 30px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <i class="fas fa-file-alt fa-3x" style="color: #ffc107; margin-bottom: 15px;"></i>
                <h4 style="color: var(--text-primary, #333); margin: 10px 0;"><?= $postsCount ?></h4>
                <p style="color: #666;">Статей</p>
            </div>
        </div>
    </div>
</div>

<div style="padding: 20px; margin-top: 20px;">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h3 style="margin-bottom: 20px; color: var(--text-primary, #333);">Быстрые действия</h3>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="/write" class="quick-action-btn">
                <i class="fas fa-pen"></i> Написать статью
            </a>
            <a href="/account/comments" class="quick-action-btn">
                <i class="fas fa-comments"></i> Мои комментарии
            </a>
            <a href="/account/edit" class="quick-action-btn">
                <i class="fas fa-user-edit"></i> Редактировать профиль
            </a>
        </div>
    </div>
</div>

<style>
.quick-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s;
}

.quick-action-btn:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

[data-theme="dark"] div[style*="background: var(--surface, #ffffff)"] {
    background: var(--surface-dark, #2d3748) !important;
}
</style>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty pagination
$greyContent6 = '';

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Личный кабинет - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>