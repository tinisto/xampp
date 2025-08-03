<?php
// User data should be available from template config
$userId = $userId ?? $_SESSION['user_id'] ?? 0;
$occupation = $occupation ?? $_SESSION["occupation"] ?? '';
$commentsCount = $commentsCount ?? 0;
$newsCount = $newsCount ?? 0;
?>

<style>
    .page-container {
        padding: 40px 0;
    }
    .content-wrapper {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 40px;
    }
    .page-title {
        font-size: 32px;
        font-weight: 600;
        color: #28a745;
        margin-bottom: 40px;
        text-align: center;
    }
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }
    .menu-item {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 25px;
        text-decoration: none;
        color: #333;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .menu-item:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-decoration: none;
        color: #333;
    }
    .menu-item i {
        font-size: 24px;
        color: #28a745;
        width: 30px;
        text-align: center;
    }
    .menu-text {
        flex: 1;
    }
    .menu-title {
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 4px;
    }
    .menu-description {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
    }
    .badge {
        background-color: #28a745;
        color: white;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 12px;
    }
    .logout-item {
        border-color: #dc3545;
    }
    .logout-item i {
        color: #dc3545;
    }
    .logout-item:hover {
        background: #fff5f5;
    }
    .delete-item {
        border-color: #dc3545;
        background: #fff5f5;
    }
    .delete-item i {
        color: #dc3545;
    }
    .delete-item:hover {
        background: #ffebeb;
    }
</style>

<div class="page-container">
    <div class="content-wrapper">
        <!-- Home link separate at top -->
        <div style="margin-bottom: 30px;">
            <a href="/" style="display: inline-flex; align-items: center; gap: 8px; color: #28a745; text-decoration: none; font-size: 16px; font-weight: 500;">
                <i class="fas fa-arrow-left"></i>
                На главную страницу
            </a>
        </div>
        
        <h1 class="page-title">Личный кабинет</h1>
        
        <div class="menu-grid">
            
            <a href="/account/personal-data-change" class="menu-item">
                <i class="fas fa-user"></i>
                <div class="menu-text">
                    <div class="menu-title">Личные данные</div>
                    <p class="menu-description">Изменить имя и контактную информацию</p>
                </div>
            </a>
            
            <a href="/account/password-change" class="menu-item">
                <i class="fas fa-lock"></i>
                <div class="menu-text">
                    <div class="menu-title">Сменить пароль</div>
                    <p class="menu-description">Обновить пароль для входа</p>
                </div>
            </a>
            
            <a href="/account/avatar" class="menu-item">
                <i class="fas fa-image"></i>
                <div class="menu-text">
                    <div class="menu-title">Аватар</div>
                    <p class="menu-description">Загрузить или изменить фото профиля</p>
                </div>
            </a>
            
            <?php if ($occupation === "Представитель ВУЗа" || 
                     $occupation === "Представитель ССУЗа" || 
                     $occupation === "Представитель школы"): ?>
            <a href="/account/representative" class="menu-item">
                <i class="fas fa-university"></i>
                <div class="menu-text">
                    <div class="menu-title">Для представителя</div>
                    <p class="menu-description">Управление учебным заведением</p>
                </div>
            </a>
            <?php endif; ?>
            
            <a href="/account/comments-user" class="menu-item">
                <i class="fas fa-comments"></i>
                <div class="menu-text">
                    <div class="menu-title">Мои комментарии</div>
                    <p class="menu-description">Просмотр всех комментариев</p>
                </div>
                <?php if ($commentsCount > 0): ?>
                <span class="badge"><?= $commentsCount ?></span>
                <?php endif; ?>
            </a>
            
            <a href="/account/news-user" class="menu-item">
                <i class="fas fa-newspaper"></i>
                <div class="menu-text">
                    <div class="menu-title">Мои новости</div>
                    <p class="menu-description">Управление публикациями</p>
                </div>
                <?php if ($newsCount > 0): ?>
                <span class="badge"><?= $newsCount ?></span>
                <?php endif; ?>
            </a>
            
            <a href="/logout" class="menu-item logout-item">
                <i class="fas fa-sign-out-alt"></i>
                <div class="menu-text">
                    <div class="menu-title">Выйти</div>
                    <p class="menu-description">Завершить текущий сеанс</p>
                </div>
            </a>
            
            <a href="/account/delete-account" class="menu-item delete-item">
                <i class="fas fa-trash-alt"></i>
                <div class="menu-text">
                    <div class="menu-title">Удалить аккаунт</div>
                    <p class="menu-description">Безвозвратное удаление</p>
                </div>
            </a>
        </div>
    </div>
</div>