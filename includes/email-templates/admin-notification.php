<?php
/**
 * Admin notification email templates
 */

function getAdminNotificationTemplate($type, $data = []) {
    $appName = '11классники';
    $currentYear = date('Y');
    $currentDate = date('d.m.Y H:i');
    
    $title = '';
    $content = '';
    
    switch($type) {
        case 'new_user':
            $title = 'Новая регистрация пользователя';
            $content = "
                <h2 style='color: #333; margin: 0 0 20px 0; font-size: 20px;'>Новый пользователь зарегистрировался</h2>
                <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                    На сайте {$appName} зарегистрировался новый пользователь.
                </p>
                <div style='background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 15px; margin: 20px 0;'>
                    <p style='color: #495057; font-size: 14px; margin: 0 0 10px 0;'><strong>Детали регистрации:</strong></p>
                    <p style='color: #495057; font-size: 14px; margin: 5px 0;'>Имя: <strong>{$data['firstname']} {$data['lastname']}</strong></p>
                    <p style='color: #495057; font-size: 14px; margin: 5px 0;'>Email: <strong>{$data['email']}</strong></p>
                    <p style='color: #495057; font-size: 14px; margin: 5px 0;'>Тип учреждения: <strong>{$data['institution_type']}</strong></p>
                    <p style='color: #495057; font-size: 14px; margin: 5px 0;'>Дата: <strong>{$currentDate}</strong></p>
                </div>
            ";
            break;
            
        case 'new_comment':
            $title = 'Новый комментарий на сайте';
            $content = "
                <h2 style='color: #333; margin: 0 0 20px 0; font-size: 20px;'>Новый комментарий</h2>
                <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                    На сайте {$appName} был оставлен новый комментарий.
                </p>
                <div style='background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 15px; margin: 20px 0;'>
                    <p style='color: #495057; font-size: 14px; margin: 0 0 10px 0;'><strong>Информация о комментарии:</strong></p>
                    <p style='color: #495057; font-size: 14px; margin: 5px 0;'>Автор: <strong>{$data['author']}</strong></p>
                    <p style='color: #495057; font-size: 14px; margin: 5px 0;'>Статья: <strong>{$data['article_title']}</strong></p>
                    <p style='color: #495057; font-size: 14px; margin: 5px 0;'>Дата: <strong>{$currentDate}</strong></p>
                    <div style='background-color: #fff; border: 1px solid #dee2e6; border-radius: 4px; padding: 10px; margin-top: 10px;'>
                        <p style='color: #495057; font-size: 14px; margin: 0;'>{$data['comment_text']}</p>
                    </div>
                </div>
            ";
            break;
            
        case 'new_message':
            $title = 'Новое сообщение через контактную форму';
            $content = "
                <h2 style='color: #333; margin: 0 0 20px 0; font-size: 20px;'>Новое сообщение</h2>
                <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                    Получено новое сообщение через контактную форму сайта {$appName}.
                </p>
                <div style='background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 15px; margin: 20px 0;'>
                    <p style='color: #495057; font-size: 14px; margin: 0 0 10px 0;'><strong>Детали сообщения:</strong></p>
                    <p style='color: #495057; font-size: 14px; margin: 5px 0;'>Имя: <strong>{$data['name']}</strong></p>
                    <p style='color: #495057; font-size: 14px; margin: 5px 0;'>Email: <strong>{$data['email']}</strong></p>
                    <p style='color: #495057; font-size: 14px; margin: 5px 0;'>Тема: <strong>{$data['subject']}</strong></p>
                    <p style='color: #495057; font-size: 14px; margin: 5px 0;'>Дата: <strong>{$currentDate}</strong></p>
                    <div style='background-color: #fff; border: 1px solid #dee2e6; border-radius: 4px; padding: 10px; margin-top: 10px;'>
                        <p style='color: #495057; font-size: 14px; margin: 0;'>{$data['message']}</p>
                    </div>
                </div>
            ";
            break;
            
        case 'database_change':
            $title = 'Изменения в базе данных';
            $content = "
                <h2 style='color: #333; margin: 0 0 20px 0; font-size: 20px;'>Изменения в базе данных</h2>
                <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                    В базе данных сайта {$appName} произошли изменения.
                </p>
                <div style='background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 15px; margin: 20px 0;'>
                    <p style='color: #856404; font-size: 14px; margin: 0 0 10px 0;'><strong>Информация об изменениях:</strong></p>
                    <p style='color: #856404; font-size: 14px; margin: 5px 0;'>Таблица: <strong>{$data['table']}</strong></p>
                    <p style='color: #856404; font-size: 14px; margin: 5px 0;'>Действие: <strong>{$data['action']}</strong></p>
                    <p style='color: #856404; font-size: 14px; margin: 5px 0;'>Пользователь: <strong>{$data['user']}</strong></p>
                    <p style='color: #856404; font-size: 14px; margin: 5px 0;'>Дата: <strong>{$currentDate}</strong></p>
                    " . (isset($data['details']) ? "
                    <div style='background-color: #fff; border: 1px solid #ffeaa7; border-radius: 4px; padding: 10px; margin-top: 10px;'>
                        <p style='color: #856404; font-size: 13px; margin: 0; font-family: monospace;'>{$data['details']}</p>
                    </div>" : "") . "
                </div>
            ";
            break;
    }
    
    // Add admin panel button for all admin emails
    $content .= "
        <table cellpadding='0' cellspacing='0' border='0' width='100%' style='margin: 30px 0;'>
            <tr>
                <td align='center'>
                    <a href='https://11klassniki.ru/dashboard' style='display: inline-block; padding: 14px 30px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px;'>Перейти в админ-панель</a>
                </td>
            </tr>
        </table>
    ";
    
    include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/base-template.php';
    return getBaseEmailTemplate($title, $content);
}
?>