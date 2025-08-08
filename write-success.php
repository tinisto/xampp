<?php
/**
 * Write success page using real_template.php
 */

// Start session to get success message
if (session_status() === PHP_SESSION_ID_NONE) {
    session_start();
}

// Get success message
$successMessage = isset($_SESSION['write_success']) ? $_SESSION['write_success'] : '';
unset($_SESSION['write_success']);

// If no success message, redirect to write page
if (empty($successMessage)) {
    header("Location: /write");
    exit();
}

// Section 1: Title
$greyContent1 = '<div style="padding: 30px; text-align: center;"><h1>Статья отправлена</h1></div>';

// Section 2: Empty
$greyContent2 = '';

// Section 3: Empty  
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Success message
$greyContent5 = '<div style="padding: 40px; max-width: 800px; margin: 0 auto;">
    <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 30px; text-align: center;">
        <div style="font-size: 48px; color: #28a745; margin-bottom: 20px;">✓</div>
        <h2 style="color: #155724; margin-bottom: 20px;">Спасибо за вашу статью!</h2>
        <p style="color: #155724; font-size: 18px; margin-bottom: 30px;">' . htmlspecialchars($successMessage) . '</p>
        
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="/write" style="display: inline-block; padding: 15px 30px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: 600;">
                Написать еще статью
            </a>
            <a href="/" style="display: inline-block; padding: 15px 30px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: 600;">
                На главную
            </a>
        </div>
    </div>
    
    <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
        <h4 style="color: #333; margin-bottom: 15px;">Что происходит дальше?</h4>
        <ol style="color: #666; margin: 0; padding-left: 20px; line-height: 1.8;">
            <li>Ваша статья отправлена на модерацию</li>
            <li>Наши редакторы проверят содержание в течение 24-48 часов</li>
            <li>После одобрения статья будет опубликована на сайте</li>
            <li>Вы получите уведомление о публикации (если вы зарегистрированы)</li>
        </ol>
    </div>
</div>';

// Section 6: Empty
$greyContent6 = '';

// Section 7: No comments
$blueContent = '';

// Page title
$pageTitle = 'Статья отправлена - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>