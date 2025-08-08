<?php
// Account edit page - migrated to use real_template.php

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
$city = $_SESSION['city'] ?? '';
$occupation = $_SESSION['occupation'] ?? '';

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Редактировать профиль', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Обновите информацию в вашем профиле'
]);
$greyContent1 = ob_get_clean();

// Section 2: Account Navigation
ob_start();
?>
<div style="padding: 20px;">
    <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
        <a href="/account" class="account-nav-item">Обзор</a>
        <a href="/account/edit" class="account-nav-item active">Редактировать профиль</a>
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

// Section 4: Empty
$greyContent4 = '';

// Section 5: Edit Profile Form
ob_start();
?>
<div style="max-width: 700px; margin: 0 auto; padding: 20px;">
    <div style="background: var(--surface, #ffffff); padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <form id="profile-edit-form" method="POST" action="/account/edit-process">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div>
                    <label for="first_name" style="display: block; margin-bottom: 8px; color: var(--text-primary, #333); font-weight: 500;">
                        Имя
                    </label>
                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($firstName) ?>" required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;
                                  background: var(--input-bg, #fff); color: var(--text-primary, #333);">
                </div>
                
                <div>
                    <label for="last_name" style="display: block; margin-bottom: 8px; color: var(--text-primary, #333); font-weight: 500;">
                        Фамилия
                    </label>
                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($lastName) ?>" required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;
                                  background: var(--input-bg, #fff); color: var(--text-primary, #333);">
                </div>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label for="email" style="display: block; margin-bottom: 8px; color: var(--text-primary, #333); font-weight: 500;">
                    Email
                </label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($userEmail) ?>" required
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;
                              background: var(--input-bg, #fff); color: var(--text-primary, #333);">
            </div>
            
            <div style="margin-bottom: 25px;">
                <label for="city" style="display: block; margin-bottom: 8px; color: var(--text-primary, #333); font-weight: 500;">
                    Город
                </label>
                <input type="text" id="city" name="city" value="<?= htmlspecialchars($city) ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;
                              background: var(--input-bg, #fff); color: var(--text-primary, #333);">
            </div>
            
            <div id="error-message" style="display: none; background: #f8d7da; color: #721c24; padding: 12px; 
                                           border-radius: 8px; margin-bottom: 20px;">
            </div>
            
            <div id="success-message" style="display: none; background: #d4edda; color: #155724; padding: 12px; 
                                             border-radius: 8px; margin-bottom: 20px;">
            </div>
            
            <div style="display: flex; gap: 15px; justify-content: flex-end;">
                <a href="/account" style="padding: 14px 28px; background: #6c757d; color: white; 
                                          text-decoration: none; border-radius: 8px; font-size: 16px; 
                                          font-weight: 500; transition: background 0.3s;">
                    Отмена
                </a>
                <button type="submit" style="padding: 14px 28px; background: #28a745; color: white; 
                                             border: none; border-radius: 8px; font-size: 16px; font-weight: 500; 
                                             cursor: pointer; transition: background 0.3s;">
                    Сохранить изменения
                </button>
            </div>
        </form>
        
    </div>
</div>

<style>
[data-theme="dark"] input {
    background: var(--surface-dark, #3a4252) !important;
    color: var(--text-primary, #e4e6eb) !important;
    border-color: #4a5568 !important;
}

[data-theme="dark"] label {
    color: var(--text-primary, #e4e6eb) !important;
}

[data-theme="dark"] div[style*="background: var(--surface, #ffffff)"] {
    background: var(--surface-dark, #2d3748) !important;
}

button[type="submit"]:hover {
    background: #218838 !important;
}

a[style*="background: #6c757d"]:hover {
    background: #5a6268 !important;
}
</style>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty pagination
$greyContent6 = '';

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Редактировать профиль - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>