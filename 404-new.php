<?php
// 404 page - migrated to use real_template.php

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Страница не найдена', [
    'fontSize' => '48px',
    'margin' => '30px 0',
    'subtitle' => 'Ошибка 404'
]);
$greyContent1 = ob_get_clean();

// Section 2: Empty navigation
$greyContent2 = '';

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Main Content
ob_start();
?>
<div style="text-align: center; padding: 40px 20px;">
    <div style="font-size: 120px; color: #28a745; opacity: 0.3; margin-bottom: 20px;">
        404
    </div>
    <p style="font-size: 18px; color: #666; margin-bottom: 30px;">
        К сожалению, запрашиваемая страница не найдена.
    </p>
    <p style="color: #666; margin-bottom: 20px;">
        Возможно, страница была перемещена или удалена.
    </p>
    <div style="margin-top: 40px;">
        <a href="/" class="btn btn-success" style="padding: 12px 30px; font-size: 16px; text-decoration: none; background: #28a745; color: white; border-radius: 25px; display: inline-block;">
            Вернуться на главную
        </a>
    </div>
    <div style="margin-top: 30px;">
        <p style="color: #999; font-size: 14px;">
            Или воспользуйтесь меню навигации
        </p>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty pagination
$greyContent6 = '';

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Страница не найдена - 404';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>