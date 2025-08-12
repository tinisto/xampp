<?php
// Error page - migrated to use real_template.php

// Get error code from query parameter or default to 500
$errorCode = $_GET['code'] ?? '500';
$errorMessages = [
    '400' => ['title' => 'Неверный запрос', 'message' => 'Сервер не может обработать запрос из-за синтаксической ошибки.'],
    '401' => ['title' => 'Не авторизован', 'message' => 'Для доступа к этой странице требуется авторизация.'],
    '403' => ['title' => 'Доступ запрещен', 'message' => 'У вас нет прав для просмотра этой страницы.'],
    '404' => ['title' => 'Страница не найдена', 'message' => 'Запрашиваемая страница не существует.'],
    '500' => ['title' => 'Внутренняя ошибка сервера', 'message' => 'На сервере произошла ошибка. Попробуйте позже.'],
    '502' => ['title' => 'Плохой шлюз', 'message' => 'Сервер получил недействительный ответ.'],
    '503' => ['title' => 'Сервис недоступен', 'message' => 'Сервер временно недоступен из-за технического обслуживания.']
];

$error = $errorMessages[$errorCode] ?? $errorMessages['500'];

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($error['title'], [
    'fontSize' => '48px',
    'margin' => '30px 0',
    'subtitle' => 'Ошибка ' . $errorCode
]);
$greyContent1 = ob_get_clean();

// Section 2: Empty navigation
$greyContent2 = '';

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Error Content
ob_start();
?>
<div style="text-align: center; padding: 40px 20px;">
    <div style="font-size: 120px; color: #dc3545; opacity: 0.3; margin-bottom: 20px;">
        <?= htmlspecialchars($errorCode) ?>
    </div>
    <p style="font-size: 18px; color: #666; margin-bottom: 30px;">
        <?= htmlspecialchars($error['message']) ?>
    </p>
    
    <div style="margin-top: 40px;">
        <a href="/" class="btn btn-success" style="padding: 12px 30px; font-size: 16px; text-decoration: none; background: #28a745; color: white; border-radius: 25px; display: inline-block; margin: 0 10px;">
            На главную
        </a>
        <a href="javascript:history.back()" class="btn btn-secondary" style="padding: 12px 30px; font-size: 16px; text-decoration: none; background: #6c757d; color: white; border-radius: 25px; display: inline-block; margin: 0 10px;">
            Назад
        </a>
    </div>
    
    <div style="margin-top: 60px; padding: 20px; background: #f8f9fa; border-radius: 8px; max-width: 600px; margin-left: auto; margin-right: auto;">
        <h3 style="color: #333; margin-bottom: 15px;">Что можно сделать?</h3>
        <ul style="text-align: left; color: #666; line-height: 1.8;">
            <li>Проверьте правильность введенного адреса</li>
            <li>Вернитесь на <a href="/" style="color: #28a745;">главную страницу</a></li>
            <li>Воспользуйтесь <a href="/search" style="color: #28a745;">поиском</a></li>
            <li>Обратитесь в <a href="/write" style="color: #28a745;">службу поддержки</a></li>
        </ul>
    </div>
</div>

<style>
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    transition: all 0.3s;
}

[data-theme="dark"] div[style*="background: #f8f9fa"] {
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
$pageTitle = $error['title'] . ' - ' . $errorCode;

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>