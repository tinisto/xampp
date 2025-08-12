<?php
// Thank you page - migrated to use real_template.php

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Спасибо!', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Ваше сообщение успешно отправлено'
]);
$greyContent1 = ob_get_clean();

// Section 2: Empty navigation
$greyContent2 = '';

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Thank You Content
ob_start();
?>
<div style="max-width: 600px; margin: 0 auto; padding: 20px; text-align: center;">
    <div style="background: var(--surface, #ffffff); padding: 60px 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <div style="margin-bottom: 30px;">
            <i class="fas fa-check-circle" style="font-size: 80px; color: #28a745;"></i>
        </div>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; font-size: 28px;">
            Ваше сообщение отправлено!
        </h2>
        
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 30px; font-size: 18px;">
            Мы получили ваше письмо и обязательно рассмотрим его в ближайшее время. 
            Если потребуется, мы свяжемся с вами по указанному адресу электронной почты.
        </p>
        
        <div style="margin-top: 40px;">
            <a href="/" class="btn-return">
                <i class="fas fa-home"></i> Вернуться на главную
            </a>
        </div>
        
    </div>
    
    <div style="margin-top: 40px;">
        <p style="color: var(--text-secondary, #666); font-size: 16px;">
            Остались вопросы? <a href="/write" style="color: #28a745;">Напишите нам еще раз</a>
        </p>
    </div>
</div>

<style>
.btn-return {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 500;
    font-size: 16px;
    transition: all 0.3s;
}

.btn-return:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
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
$pageTitle = 'Спасибо - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>