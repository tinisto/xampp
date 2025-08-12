<?php
// Unauthorized page - migrated to use real_template.php

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Доступ запрещен', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'У вас нет прав для просмотра этой страницы'
]);
$greyContent1 = ob_get_clean();

// Section 2: Empty navigation
$greyContent2 = '';

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Unauthorized Content
ob_start();
?>
<div style="max-width: 600px; margin: 0 auto; padding: 20px; text-align: center;">
    <div style="background: var(--surface, #ffffff); padding: 60px 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <div style="margin-bottom: 30px;">
            <i class="fas fa-lock" style="font-size: 80px; color: #dc3545;"></i>
        </div>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; font-size: 28px;">
            Ошибка 403
        </h2>
        
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 30px; font-size: 18px;">
            У вас недостаточно прав для доступа к этой странице. 
            Если вы считаете, что это ошибка, пожалуйста, свяжитесь с администратором.
        </p>
        
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-top: 40px;">
            <a href="/" class="btn-action btn-primary">
                <i class="fas fa-home"></i> На главную
            </a>
            <a href="/login" class="btn-action btn-secondary">
                <i class="fas fa-sign-in-alt"></i> Войти
            </a>
        </div>
        
    </div>
    
    <div style="margin-top: 40px;">
        <p style="color: var(--text-secondary, #666); font-size: 16px;">
            Нужна помощь? <a href="/write" style="color: #28a745;">Напишите нам</a>
        </p>
    </div>
</div>

<style>
.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 500;
    font-size: 16px;
    transition: all 0.3s;
}

.btn-primary {
    background: #28a745;
    color: white;
}

.btn-primary:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    color: white;
    text-decoration: none;
}

.btn-secondary {
    background: #007bff;
    color: white;
}

.btn-secondary:hover {
    background: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
    color: white;
    text-decoration: none;
}

[data-theme="dark"] h2 {
    color: var(--text-primary, #e4e6eb) !important;
}

[data-theme="dark"] p {
    color: var(--text-secondary, #b0b3b8) !important;
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
$pageTitle = 'Доступ запрещен - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>