<?php
// Email notification system

class EmailNotification {
    private static $fromEmail = 'noreply@11klassniki.ru';
    private static $fromName = '11klassniki.ru';
    
    // Send welcome email when user registers
    public static function sendWelcomeEmail($userEmail, $userName) {
        $subject = 'Добро пожаловать на 11klassniki.ru!';
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .button { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Добро пожаловать, {$userName}!</h1>
                </div>
                <div class='content'>
                    <p>Спасибо за регистрацию на портале 11klassniki.ru!</p>
                    <p>Теперь вы можете:</p>
                    <ul>
                        <li>Добавлять статьи в избранное</li>
                        <li>Оставлять комментарии</li>
                        <li>Получать персональные рекомендации</li>
                        <li>Следить за новостями образования</li>
                    </ul>
                    <p style='text-align: center; margin-top: 30px;'>
                        <a href='https://11klassniki.ru/profile' class='button'>Перейти в профиль</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " 11klassniki.ru - Портал образования России</p>
                    <p><a href='https://11klassniki.ru/privacy'>Политика конфиденциальности</a></p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $textBody = "Добро пожаловать, {$userName}!\n\n";
        $textBody .= "Спасибо за регистрацию на портале 11klassniki.ru!\n\n";
        $textBody .= "Теперь вы можете:\n";
        $textBody .= "- Добавлять статьи в избранное\n";
        $textBody .= "- Оставлять комментарии\n";
        $textBody .= "- Получать персональные рекомендации\n";
        $textBody .= "- Следить за новостями образования\n\n";
        $textBody .= "Перейти в профиль: https://11klassniki.ru/profile\n\n";
        $textBody .= "© " . date('Y') . " 11klassniki.ru";
        
        return self::sendEmail($userEmail, $subject, $htmlBody, $textBody);
    }
    
    // Send password reset email
    public static function sendPasswordResetEmail($userEmail, $userName, $resetToken) {
        $subject = 'Восстановление пароля на 11klassniki.ru';
        $resetLink = "https://11klassniki.ru/reset-password?token={$resetToken}";
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .button { display: inline-block; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Восстановление пароля</h1>
                </div>
                <div class='content'>
                    <p>Здравствуйте, {$userName}!</p>
                    <p>Мы получили запрос на восстановление пароля для вашего аккаунта.</p>
                    <p>Для создания нового пароля перейдите по ссылке:</p>
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='{$resetLink}' class='button'>Восстановить пароль</a>
                    </p>
                    <p><small>Ссылка действительна в течение 24 часов.</small></p>
                    <p><small>Если вы не запрашивали восстановление пароля, просто проигнорируйте это письмо.</small></p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " 11klassniki.ru - Портал образования России</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $textBody = "Здравствуйте, {$userName}!\n\n";
        $textBody .= "Мы получили запрос на восстановление пароля для вашего аккаунта.\n\n";
        $textBody .= "Для создания нового пароля перейдите по ссылке:\n";
        $textBody .= "{$resetLink}\n\n";
        $textBody .= "Ссылка действительна в течение 24 часов.\n\n";
        $textBody .= "Если вы не запрашивали восстановление пароля, просто проигнорируйте это письмо.\n\n";
        $textBody .= "© " . date('Y') . " 11klassniki.ru";
        
        return self::sendEmail($userEmail, $subject, $htmlBody, $textBody);
    }
    
    // Send comment notification
    public static function sendCommentNotification($userEmail, $userName, $itemTitle, $commentAuthor, $commentText) {
        $subject = 'Новый комментарий к вашей статье';
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .comment-box { background: white; padding: 15px; border-left: 3px solid #28a745; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Новый комментарий</h1>
                </div>
                <div class='content'>
                    <p>Здравствуйте, {$userName}!</p>
                    <p>{$commentAuthor} оставил комментарий к материалу \"{$itemTitle}\":</p>
                    <div class='comment-box'>
                        <strong>{$commentAuthor}:</strong><br>
                        {$commentText}
                    </div>
                    <p><a href='https://11klassniki.ru/'>Перейти к материалу</a></p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " 11klassniki.ru - Портал образования России</p>
                    <p><a href='https://11klassniki.ru/settings'>Настройки уведомлений</a></p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $textBody = "Здравствуйте, {$userName}!\n\n";
        $textBody .= "{$commentAuthor} оставил комментарий к материалу \"{$itemTitle}\":\n\n";
        $textBody .= "{$commentText}\n\n";
        $textBody .= "Перейти к материалу: https://11klassniki.ru/\n\n";
        $textBody .= "© " . date('Y') . " 11klassniki.ru";
        
        return self::sendEmail($userEmail, $subject, $htmlBody, $textBody);
    }
    
    // Send weekly digest
    public static function sendWeeklyDigest($userEmail, $userName, $newsItems) {
        $subject = 'Еженедельный дайджест новостей образования';
        
        $newsHtml = '';
        foreach ($newsItems as $news) {
            $newsHtml .= "
                <div style='margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #dee2e6;'>
                    <h3 style='margin: 0 0 10px 0;'><a href='https://11klassniki.ru/news/{$news['url_news']}' style='color: #007bff; text-decoration: none;'>{$news['title_news']}</a></h3>
                    <p style='margin: 0; color: #666;'>" . mb_substr(strip_tags($news['text_news']), 0, 200) . "...</p>
                </div>
            ";
        }
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Еженедельный дайджест</h1>
                </div>
                <div class='content'>
                    <p>Здравствуйте, {$userName}!</p>
                    <p>Вот самые важные новости образования за прошедшую неделю:</p>
                    {$newsHtml}
                    <p style='text-align: center; margin-top: 30px;'>
                        <a href='https://11klassniki.ru/news' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Все новости</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " 11klassniki.ru - Портал образования России</p>
                    <p><a href='https://11klassniki.ru/settings'>Отписаться от рассылки</a></p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $textBody = "Здравствуйте, {$userName}!\n\n";
        $textBody .= "Вот самые важные новости образования за прошедшую неделю:\n\n";
        foreach ($newsItems as $news) {
            $textBody .= "- {$news['title_news']}\n";
            $textBody .= "  " . mb_substr(strip_tags($news['text_news']), 0, 100) . "...\n";
            $textBody .= "  Читать: https://11klassniki.ru/news/{$news['url_news']}\n\n";
        }
        $textBody .= "Все новости: https://11klassniki.ru/news\n\n";
        $textBody .= "© " . date('Y') . " 11klassniki.ru";
        
        return self::sendEmail($userEmail, $subject, $htmlBody, $textBody);
    }
    
    // Send event reminder email
    public static function sendEventReminder($eventData) {
        $subject = 'Напоминание о событии: ' . $eventData['title'];
        
        $eventDate = date('d.m.Y', strtotime($eventData['start_date']));
        $eventTime = $eventData['start_time'] ? date('H:i', strtotime($eventData['start_time'])) : '';
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #ffc107; color: #212529; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .event-box { background: white; padding: 20px; border-left: 4px solid #ffc107; margin: 20px 0; border-radius: 4px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .button { display: inline-block; padding: 12px 24px; background: #ffc107; color: #212529; text-decoration: none; border-radius: 5px; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>⏰ Напоминание о событии</h1>
                </div>
                <div class='content'>
                    <p>Здравствуйте, {$eventData['user_name']}!</p>
                    <p>Напоминаем о предстоящем событии:</p>
                    <div class='event-box'>
                        <h2 style='margin: 0 0 15px 0; color: #212529;'>{$eventData['title']}</h2>
                        <p><strong>📅 Дата:</strong> {$eventDate}" . ($eventTime ? " в {$eventTime}" : "") . "</p>";
        
        if ($eventData['location']) {
            $htmlBody .= "<p><strong>📍 Место:</strong> {$eventData['location']}</p>";
        }
        
        if ($eventData['description']) {
            $htmlBody .= "<p><strong>📝 Описание:</strong></p><p>" . nl2br(htmlspecialchars($eventData['description'])) . "</p>";
        }
        
        $htmlBody .= "
                    </div>
                    <p style='text-align: center; margin-top: 30px;'>
                        <a href='https://11klassniki.ru/event/{$eventData['event_id']}' class='button'>Подробнее о событии</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " 11klassniki.ru - Портал образования России</p>
                    <p><a href='https://11klassniki.ru/events'>Календарь событий</a> | <a href='https://11klassniki.ru/settings'>Настройки уведомлений</a></p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $textBody = "Напоминание о событии\n\n";
        $textBody .= "Здравствуйте, {$eventData['user_name']}!\n\n";
        $textBody .= "Напоминаем о предстоящем событии:\n\n";
        $textBody .= "СОБЫТИЕ: {$eventData['title']}\n";
        $textBody .= "ДАТА: {$eventDate}" . ($eventTime ? " в {$eventTime}" : "") . "\n";
        if ($eventData['location']) {
            $textBody .= "МЕСТО: {$eventData['location']}\n";
        }
        if ($eventData['description']) {
            $textBody .= "ОПИСАНИЕ: " . strip_tags($eventData['description']) . "\n";
        }
        $textBody .= "\nПодробнее: https://11klassniki.ru/event/{$eventData['event_id']}\n\n";
        $textBody .= "Календарь событий: https://11klassniki.ru/events\n\n";
        $textBody .= "© " . date('Y') . " 11klassniki.ru";
        
        return self::sendEmail($eventData['email'], $subject, $htmlBody, $textBody);
    }
    
    // Send new event notification
    public static function sendNewEventNotification($userEmail, $userName, $eventData) {
        $subject = 'Новое событие: ' . $eventData['title'];
        
        $eventDate = date('d.m.Y', strtotime($eventData['start_date']));
        $eventTime = $eventData['start_time'] ? date('H:i', strtotime($eventData['start_time'])) : '';
        
        $typeLabels = [
            'deadline' => '⏰ Дедлайн',
            'exam' => '📝 Экзамен',
            'open_day' => '🏛️ День открытых дверей',
            'conference' => '🎤 Конференция',
            'other' => '📅 Событие'
        ];
        $typeLabel = $typeLabels[$eventData['event_type']] ?? '📅 Событие';
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .event-box { background: white; padding: 20px; border-left: 4px solid #28a745; margin: 20px 0; border-radius: 4px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .button { display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔔 Новое событие</h1>
                </div>
                <div class='content'>
                    <p>Здравствуйте, {$userName}!</p>
                    <p>В календаре событий появилось новое мероприятие:</p>
                    <div class='event-box'>
                        <h2 style='margin: 0 0 15px 0; color: #28a745;'>{$typeLabel} {$eventData['title']}</h2>
                        <p><strong>📅 Дата:</strong> {$eventDate}" . ($eventTime ? " в {$eventTime}" : "") . "</p>";
        
        if ($eventData['location']) {
            $htmlBody .= "<p><strong>📍 Место:</strong> {$eventData['location']}</p>";
        }
        
        if ($eventData['organizer']) {
            $htmlBody .= "<p><strong>👥 Организатор:</strong> {$eventData['organizer']}</p>";
        }
        
        $htmlBody .= "
                    </div>
                    <p style='text-align: center; margin-top: 30px;'>
                        <a href='https://11klassniki.ru/event/{$eventData['id']}' class='button'>Подробнее и подписка на напоминания</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " 11klassniki.ru - Портал образования России</p>
                    <p><a href='https://11klassniki.ru/events'>Все события</a> | <a href='https://11klassniki.ru/settings'>Настройки уведомлений</a></p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $textBody = "Новое событие в календаре\n\n";
        $textBody .= "Здравствуйте, {$userName}!\n\n";
        $textBody .= "В календаре событий появилось новое мероприятие:\n\n";
        $textBody .= "ТИП: {$typeLabel}\n";
        $textBody .= "СОБЫТИЕ: {$eventData['title']}\n";
        $textBody .= "ДАТА: {$eventDate}" . ($eventTime ? " в {$eventTime}" : "") . "\n";
        if ($eventData['location']) {
            $textBody .= "МЕСТО: {$eventData['location']}\n";
        }
        if ($eventData['organizer']) {
            $textBody .= "ОРГАНИЗАТОР: {$eventData['organizer']}\n";
        }
        $textBody .= "\nПодробнее и подписка на напоминания: https://11klassniki.ru/event/{$eventData['id']}\n\n";
        $textBody .= "Все события: https://11klassniki.ru/events\n\n";
        $textBody .= "© " . date('Y') . " 11klassniki.ru";
        
        return self::sendEmail($userEmail, $subject, $htmlBody, $textBody);
    }
    
    // Core email sending function
    private static function sendEmail($to, $subject, $htmlBody, $textBody) {
        // For local development, just log emails
        if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'localhost') {
            $logDir = $_SERVER['DOCUMENT_ROOT'] . '/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            
            $logFile = $logDir . '/emails.log';
            $logEntry = date('Y-m-d H:i:s') . " | To: {$to} | Subject: {$subject}\n";
            $logEntry .= "HTML Body:\n{$htmlBody}\n";
            $logEntry .= "Text Body:\n{$textBody}\n";
            $logEntry .= str_repeat('-', 80) . "\n\n";
            
            file_put_contents($logFile, $logEntry, FILE_APPEND);
            return true;
        }
        
        // For production, use mail() or PHPMailer
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="boundary-' . uniqid() . '"',
            'From: ' . self::$fromName . ' <' . self::$fromEmail . '>',
            'Reply-To: ' . self::$fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        $boundary = 'boundary-' . uniqid();
        
        $message = "--{$boundary}\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $textBody . "\r\n\r\n";
        
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $htmlBody . "\r\n\r\n";
        
        $message .= "--{$boundary}--";
        
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
}
?>