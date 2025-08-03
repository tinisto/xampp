<?php
/**
 * Password changed notification email template
 */

function getPasswordChangedEmailTemplate($firstname = '') {
    $appName = '11классники';
    $currentYear = date('Y');
    $currentDate = date('d.m.Y');
    $currentTime = date('H:i');
    
    // If firstname is not provided, use a generic greeting
    $greeting = $firstname ? "Здравствуйте, {$firstname}!" : "Здравствуйте!";
    
    return "
<!DOCTYPE html>
<html lang='ru'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Пароль изменен</title>
</head>
<body style='margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Arial, sans-serif; background-color: #f5f5f5;'>
    <table cellpadding='0' cellspacing='0' border='0' width='100%' style='background-color: #f5f5f5; padding: 20px 0;'>
        <tr>
            <td align='center'>
                <table cellpadding='0' cellspacing='0' border='0' width='600' style='background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                    <!-- Header -->
                    <tr>
                        <td style='background: linear-gradient(135deg, #28a745, #218838); padding: 30px; text-align: center; border-radius: 8px 8px 0 0;'>
                            <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>{$appName}</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style='padding: 40px 30px;'>
                            <h2 style='color: #333; margin: 0 0 20px 0; font-size: 20px;'>Пароль успешно изменен</h2>
                            
                            <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                                {$greeting}
                            </p>
                            
                            <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                                Ваш пароль был успешно изменен {$currentDate} в {$currentTime}.
                            </p>
                            
                            <div style='background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; padding: 15px; margin: 20px 0;'>
                                <p style='color: #155724; font-size: 14px; margin: 0;'>
                                    <strong>✓ Пароль изменен</strong><br>
                                    Теперь вы можете использовать новый пароль для входа в систему.
                                </p>
                            </div>
                            
                            <div style='background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 15px; margin: 20px 0;'>
                                <p style='color: #856404; font-size: 14px; margin: 0 0 10px 0;'>
                                    <strong>Важная информация о безопасности:</strong>
                                </p>
                                <p style='color: #856404; font-size: 14px; margin: 0;'>
                                    Если вы не меняли пароль, немедленно свяжитесь с нами по адресу 
                                    <a href='mailto:support@11klassniki.ru' style='color: #856404;'>support@11klassniki.ru</a>
                                </p>
                            </div>
                            
                            <h3 style='color: #333; margin: 30px 0 15px 0; font-size: 16px;'>Советы по безопасности:</h3>
                            <ul style='color: #666; font-size: 14px; line-height: 1.8; margin: 0 0 20px 0; padding-left: 20px;'>
                                <li>Используйте уникальный пароль для каждого сайта</li>
                                <li>Не делитесь паролем с другими людьми</li>
                                <li>Регулярно обновляйте пароль</li>
                                <li>Используйте сложные пароли с буквами, цифрами и символами</li>
                            </ul>
                            
                            <!-- Button -->
                            <table cellpadding='0' cellspacing='0' border='0' width='100%' style='margin: 30px 0;'>
                                <tr>
                                    <td align='center'>
                                        <a href='https://11klassniki.ru/login' style='display: inline-block; padding: 14px 30px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px;'>Войти в аккаунт</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style='background-color: #f8f9fa; padding: 30px; text-align: center; border-radius: 0 0 8px 8px;'>
                            <p style='color: #999; font-size: 13px; margin: 0 0 10px 0;'>
                                С уважением,<br>
                                Команда {$appName}
                            </p>
                            <p style='color: #999; font-size: 12px; margin: 0 0 10px 0;'>
                                © {$currentYear} {$appName}. Все права защищены.
                            </p>
                            <p style='color: #999; font-size: 11px; margin: 0;'>
                                Если у вас есть вопросы, обращайтесь: support@11klassniki.ru
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
    ";
}
?>