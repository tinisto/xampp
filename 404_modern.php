<?php
// Modern 404 page
$pageTitle = 'Страница не найдена';

// Section 1: 404 message
ob_start();
?>
<div style="padding: 100px 20px; text-align: center;">
    <h1 style="font-size: 120px; font-weight: 700; color: #dee2e6; margin: 0;">404</h1>
    <h2 style="font-size: 32px; font-weight: 600; color: #333; margin: 20px 0;">Страница не найдена</h2>
    <p style="font-size: 18px; color: #6c757d; margin-bottom: 40px;">
        К сожалению, запрашиваемая страница не существует или была удалена.
    </p>
    
    <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
        <a href="/" 
           style="display: inline-block; background: #007bff; color: white; padding: 15px 30px; border-radius: 8px; text-decoration: none; font-weight: 500;">
            ← На главную
        </a>
        <a href="/news" 
           style="display: inline-block; background: #6c757d; color: white; padding: 15px 30px; border-radius: 8px; text-decoration: none; font-weight: 500;">
            Читать новости
        </a>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Other sections empty
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';
$blueContent = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>