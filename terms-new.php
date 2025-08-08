<?php
// Terms page - migrated to use real_template.php

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Условия использования', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Последнее обновление: ' . date('d.m.Y')
]);
$greyContent1 = ob_get_clean();

// Section 2: Empty navigation
$greyContent2 = '';

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Terms Content
ob_start();
?>
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <div style="background: var(--surface, #ffffff); padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px;">1. Общие условия</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            Используя сайт 11klassniki.ru, вы соглашаетесь с настоящими условиями использования. 
            Если вы не согласны с какими-либо условиями, пожалуйста, не используйте наш сайт.
        </p>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">2. Регистрация и аккаунт</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 15px;">
            При регистрации вы обязуетесь:
        </p>
        <ul style="color: var(--text-secondary, #666); line-height: 1.8; margin-left: 20px; margin-bottom: 20px;">
            <li>Предоставлять достоверную информацию</li>
            <li>Поддерживать актуальность своих данных</li>
            <li>Сохранять конфиденциальность пароля</li>
            <li>Нести ответственность за все действия под своим аккаунтом</li>
        </ul>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">3. Правила поведения</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 15px;">
            Пользователям запрещается:
        </p>
        <ul style="color: var(--text-secondary, #666); line-height: 1.8; margin-left: 20px; margin-bottom: 20px;">
            <li>Размещать недостоверную информацию</li>
            <li>Нарушать права других пользователей</li>
            <li>Использовать нецензурную лексику</li>
            <li>Размещать спам и рекламу без согласования</li>
            <li>Пытаться получить несанкционированный доступ</li>
        </ul>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">4. Контент пользователей</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            Размещая контент на сайте, вы:
        </p>
        <ul style="color: var(--text-secondary, #666); line-height: 1.8; margin-left: 20px; margin-bottom: 20px;">
            <li>Гарантируете, что обладаете правами на размещаемый контент</li>
            <li>Предоставляете нам право использовать этот контент</li>
            <li>Несете ответственность за размещенную информацию</li>
        </ul>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">5. Интеллектуальная собственность</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            Все материалы сайта, включая тексты, изображения, дизайн, являются объектами 
            интеллектуальной собственности и защищены законодательством РФ.
        </p>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">6. Ограничение ответственности</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            Администрация сайта не несет ответственности за:
        </p>
        <ul style="color: var(--text-secondary, #666); line-height: 1.8; margin-left: 20px; margin-bottom: 20px;">
            <li>Действия пользователей на сайте</li>
            <li>Качество и достоверность размещенной информации</li>
            <li>Прямые или косвенные убытки от использования сайта</li>
        </ul>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">7. Изменение условий</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            Мы оставляем за собой право изменять данные условия. Продолжая использовать сайт после 
            внесения изменений, вы соглашаетесь с новыми условиями.
        </p>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">8. Применимое право</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            Настоящие условия регулируются законодательством Российской Федерации. Все споры 
            решаются в соответствии с действующим законодательством РФ.
        </p>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">9. Контакты</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            По всем вопросам вы можете обратиться к нам через 
            <a href="/write" style="color: #28a745;">форму обратной связи</a>.
        </p>
        
    </div>
</div>

<style>
[data-theme="dark"] h2 {
    color: var(--text-primary, #e4e6eb) !important;
}

[data-theme="dark"] p,
[data-theme="dark"] li {
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
$pageTitle = 'Условия использования - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>