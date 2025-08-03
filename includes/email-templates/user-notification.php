<?php
/**
 * User notification email templates
 */

function getUserNotificationTemplate($type, $data = []) {
    $appName = '11классники';
    
    $title = '';
    $content = '';
    $buttonText = '';
    $buttonUrl = '';
    
    switch($type) {
        case 'welcome':
            $title = 'Добро пожаловать на ' . $appName;
            $greeting = isset($data['firstname']) ? "Здравствуйте, {$data['firstname']}!" : "Здравствуйте!";
            $content = "
                <h2 style='color: #333; margin: 0 0 20px 0; font-size: 20px;'>Добро пожаловать!</h2>
                <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                    {$greeting}
                </p>
                <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                    Спасибо за регистрацию на {$appName}! Мы рады приветствовать вас в нашем сообществе.
                </p>
                <div style='background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; padding: 15px; margin: 20px 0;'>
                    <p style='color: #155724; font-size: 14px; margin: 0 0 10px 0;'>
                        <strong>Что вы можете делать на нашем сайте:</strong>
                    </p>
                    <ul style='color: #155724; font-size: 14px; margin: 0; padding-left: 20px;'>
                        <li>Читать последние новости об образовании</li>
                        <li>Находить информацию об учебных заведениях</li>
                        <li>Проходить тесты и отслеживать свой прогресс</li>
                        <li>Участвовать в обсуждениях</li>
                    </ul>
                </div>
            ";
            $buttonText = 'Перейти на сайт';
            $buttonUrl = 'https://11klassniki.ru';
            break;
            
        case 'account_suspended':
            $title = 'Аккаунт временно заблокирован';
            $content = "
                <h2 style='color: #333; margin: 0 0 20px 0; font-size: 20px;'>Ваш аккаунт временно заблокирован</h2>
                <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                    К сожалению, ваш аккаунт на {$appName} был временно заблокирован.
                </p>
                <div style='background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; padding: 15px; margin: 20px 0;'>
                    <p style='color: #721c24; font-size: 14px; margin: 0;'>
                        <strong>Причина:</strong> {$data['reason']}
                    </p>
                </div>
                <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 20px 0;'>
                    Если вы считаете, что это произошло по ошибке, пожалуйста, свяжитесь с нами по адресу support@11klassniki.ru
                </p>
            ";
            break;
            
        case 'account_unsuspended':
            $title = 'Аккаунт разблокирован';
            $content = "
                <h2 style='color: #333; margin: 0 0 20px 0; font-size: 20px;'>Ваш аккаунт разблокирован</h2>
                <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                    Хорошие новости! Ваш аккаунт на {$appName} был разблокирован.
                </p>
                <div style='background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; padding: 15px; margin: 20px 0;'>
                    <p style='color: #155724; font-size: 14px; margin: 0;'>
                        Теперь вы снова можете пользоваться всеми функциями сайта.
                    </p>
                </div>
                <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 20px 0;'>
                    Благодарим за понимание и надеемся на дальнейшее сотрудничество.
                </p>
            ";
            $buttonText = 'Войти в аккаунт';
            $buttonUrl = 'https://11klassniki.ru/login';
            break;
            
        case 'comment_reply':
            $title = 'Ответ на ваш комментарий';
            $content = "
                <h2 style='color: #333; margin: 0 0 20px 0; font-size: 20px;'>На ваш комментарий ответили</h2>
                <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                    Кто-то ответил на ваш комментарий к статье <strong>\"{$data['article_title']}\"</strong>.
                </p>
                <div style='background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 15px; margin: 20px 0;'>
                    <p style='color: #495057; font-size: 14px; margin: 0 0 10px 0;'>
                        <strong>Ваш комментарий:</strong>
                    </p>
                    <div style='background-color: #fff; border-left: 3px solid #28a745; padding: 10px; margin-bottom: 15px;'>
                        <p style='color: #495057; font-size: 14px; margin: 0;'>{$data['original_comment']}</p>
                    </div>
                    <p style='color: #495057; font-size: 14px; margin: 0 0 10px 0;'>
                        <strong>Ответ от {$data['reply_author']}:</strong>
                    </p>
                    <div style='background-color: #fff; border-left: 3px solid #007bff; padding: 10px;'>
                        <p style='color: #495057; font-size: 14px; margin: 0;'>{$data['reply_text']}</p>
                    </div>
                </div>
            ";
            $buttonText = 'Посмотреть обсуждение';
            $buttonUrl = $data['article_url'] . '#comments';
            break;
    }
    
    include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/base-template.php';
    return getBaseEmailTemplate($title, $content, $buttonText, $buttonUrl);
}
?>