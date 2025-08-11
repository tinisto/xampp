<?php
session_start();

$page_title = 'Доступ запрещен - 11klassniki.ru';

// Section content
ob_start();
?>
<div style="padding: 60px 20px; background: white; text-align: center;">
    <div style="max-width: 600px; margin: 0 auto;">
        <i class="fas fa-lock" style="font-size: 80px; color: #dc3545; margin-bottom: 30px;"></i>
        <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 20px; color: #333;">Доступ запрещен</h1>
        <p style="font-size: 18px; color: #666; margin-bottom: 40px; line-height: 1.6;">
            У вас нет прав для доступа к этой странице. Эта область предназначена только для администраторов системы.
        </p>
        
        <?php if (isset($_SESSION['user_id'])): ?>
        <p style="color: #999; margin-bottom: 30px;">
            Вы вошли как: <?= htmlspecialchars($_SESSION['user_email'] ?? 'пользователь') ?>
            <br>Ваша роль: <?= $_SESSION['user_role'] === 'admin' ? 'Администратор' : 'Пользователь' ?>
        </p>
        
        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
            <a href="/" 
               style="display: inline-block; padding: 12px 30px; background: #0039A6; color: white; 
                      border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-home"></i> На главную
            </a>
            <a href="/profile_modern.php" 
               style="display: inline-block; padding: 12px 30px; background: #666; color: white; 
                      border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-user"></i> Профиль
            </a>
        </div>
        <?php else: ?>
        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
            <a href="/login_modern.php" 
               style="display: inline-block; padding: 12px 30px; background: #0039A6; color: white; 
                      border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-sign-in-alt"></i> Войти
            </a>
            <a href="/" 
               style="display: inline-block; padding: 12px 30px; background: #666; color: white; 
                      border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-home"></i> На главную
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';
$blueContent = '';

include $_SERVER['DOCUMENT_ROOT'] . '/template.php';
?>