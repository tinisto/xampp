<?php
/**
 * Account activation email template
 */

function getAccountActivationEmailTemplate($firstname, $activationLink) {
    $appName = '11классники';
    $currentYear = date('Y');
    
    return "
<!DOCTYPE html>
<html lang='ru'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Активация аккаунта</title>
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
                            <h2 style='color: #333; margin: 0 0 20px 0; font-size: 20px;'>Добро пожаловать!</h2>
                            
                            <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                                Здравствуйте, {$firstname}!
                            </p>
                            
                            <p style='color: #666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;'>
                                Спасибо за регистрацию на {$appName}. Для завершения регистрации и активации вашего аккаунта, пожалуйста, нажмите на кнопку ниже:
                            </p>
                            
                            <!-- Button -->
                            <table cellpadding='0' cellspacing='0' border='0' width='100%' style='margin: 30px 0;'>
                                <tr>
                                    <td align='center'>
                                        <a href='{$activationLink}' style='display: inline-block; padding: 14px 30px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px;'>Активировать аккаунт</a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style='color: #666; font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;'>
                                Или скопируйте эту ссылку в браузер:
                            </p>
                            
                            <p style='color: #3498db; font-size: 14px; word-break: break-all; line-height: 1.6; margin: 0 0 20px 0;'>
                                {$activationLink}
                            </p>
                            
                            <div style='background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; padding: 15px; margin: 20px 0;'>
                                <p style='color: #155724; font-size: 14px; margin: 0 0 10px 0;'>
                                    <strong>После активации вы сможете:</strong>
                                </p>
                                <ul style='color: #155724; font-size: 14px; margin: 0; padding-left: 20px;'>
                                    <li>Получать персонализированные новости об образовании</li>
                                    <li>Сохранять интересующие учебные заведения</li>
                                    <li>Оставлять комментарии и участвовать в обсуждениях</li>
                                    <li>Проходить тесты и отслеживать прогресс</li>
                                </ul>
                            </div>
                            
                            <p style='color: #666; font-size: 14px; line-height: 1.6; margin: 20px 0 0 0;'>
                                Если у вас возникли вопросы, не стесняйтесь обращаться к нам по адресу support@11klassniki.ru
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style='background-color: #f8f9fa; padding: 30px; text-align: center; border-radius: 0 0 8px 8px;'>
                            <p style='color: #999; font-size: 13px; margin: 0 0 10px 0;'>
                                С уважением,<br>
                                Команда {$appName}
                            </p>
                            <p style='color: #999; font-size: 12px; margin: 0;'>
                                © {$currentYear} {$appName}. Все права защищены.
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