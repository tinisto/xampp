<?php
// Privacy page - migrated to use real_template.php

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Политика конфиденциальности', [
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

// Section 5: Privacy Content
ob_start();
?>
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <div style="background: var(--surface, #ffffff); padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px;">1. Общие положения</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            Настоящая политика конфиденциальности (далее – Политика) действует в отношении всей информации, 
            которую сайт 11klassniki.ru может получить о пользователе во время использования сайта.
        </p>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">2. Сбор информации</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 15px;">
            Мы собираем следующую информацию:
        </p>
        <ul style="color: var(--text-secondary, #666); line-height: 1.8; margin-left: 20px; margin-bottom: 20px;">
            <li>Имя и фамилия</li>
            <li>Адрес электронной почты</li>
            <li>Информация о посещаемых страницах</li>
            <li>IP-адрес</li>
            <li>Информация о браузере и устройстве</li>
        </ul>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">3. Использование информации</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 15px;">
            Собранная информация используется для:
        </p>
        <ul style="color: var(--text-secondary, #666); line-height: 1.8; margin-left: 20px; margin-bottom: 20px;">
            <li>Предоставления доступа к функциям сайта</li>
            <li>Улучшения качества обслуживания</li>
            <li>Связи с пользователями</li>
            <li>Проведения статистических исследований</li>
            <li>Обеспечения безопасности</li>
        </ul>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">4. Защита данных</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            Мы принимаем необходимые организационные и технические меры для защиты персональной информации 
            пользователей от неправомерного или случайного доступа, уничтожения, изменения, блокирования, 
            копирования, распространения.
        </p>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">5. Использование cookies</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            Сайт использует файлы cookies для улучшения работы сайта. Cookies представляют собой небольшие 
            файлы данных, которые сохраняются на вашем устройстве. Вы можете отключить использование cookies 
            в настройках браузера.
        </p>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">6. Передача данных третьим лицам</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            Мы не передаем персональные данные пользователей третьим лицам без согласия пользователей, 
            за исключением случаев, предусмотренных законодательством РФ.
        </p>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">7. Права пользователей</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 15px;">
            Пользователи имеют право:
        </p>
        <ul style="color: var(--text-secondary, #666); line-height: 1.8; margin-left: 20px; margin-bottom: 20px;">
            <li>Получать информацию о своих персональных данных</li>
            <li>Требовать уточнения или удаления своих данных</li>
            <li>Отозвать согласие на обработку данных</li>
            <li>Обратиться с жалобой в уполномоченный орган</li>
        </ul>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">8. Изменения в политике</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            Мы оставляем за собой право вносить изменения в настоящую Политику. При внесении изменений 
            в актуальной редакции указывается дата последнего обновления.
        </p>
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 20px; margin-top: 30px;">9. Контакты</h2>
        <p style="color: var(--text-secondary, #666); line-height: 1.8; margin-bottom: 20px;">
            По всем вопросам, связанным с настоящей Политикой, вы можете связаться с нами через 
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
$pageTitle = 'Политика конфиденциальности - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>