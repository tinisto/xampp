<?php
/**
 * Modern Email Service
 * Centralized email sending with consistent templates
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/PHPMailer/src/PHPMailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/PHPMailer/src/SMTP.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private static $instance = null;
    private $mailer;
    
    private function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new EmailService();
        }
        return self::$instance;
    }
    
    private function configureMailer() {
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['SMTP_USERNAME'] ?? '';
        $this->mailer->Password = $_ENV['SMTP_PASSWORD'] ?? '';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $_ENV['SMTP_PORT'] ?? 587;
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->setFrom($_ENV['SMTP_FROM_EMAIL'] ?? 'noreply@11klassniki.ru', '11классники');
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordReset($email, $firstname, $resetLink) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/password-reset.php';
        
        $subject = 'Восстановление пароля - 11классники';
        $body = getPasswordResetEmailTemplate($firstname, $resetLink);
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send password changed notification
     */
    public function sendPasswordChanged($email, $firstname = '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/password-changed.php';
        
        $subject = 'Пароль изменен - 11классники';
        $body = getPasswordChangedEmailTemplate($firstname);
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send account activation email
     */
    public function sendAccountActivation($email, $firstname, $activationLink) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/account-activation.php';
        
        $subject = 'Активация аккаунта - 11классники';
        $body = getAccountActivationEmailTemplate($firstname, $activationLink);
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send admin notification
     */
    public function sendAdminNotification($type, $data = []) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/admin-notification.php';
        
        $adminEmail = $_ENV['ADMIN_EMAIL'] ?? 'admin@11klassniki.ru';
        $subjects = [
            'new_user' => 'Новая регистрация - 11классники',
            'new_comment' => 'Новый комментарий - 11классники',
            'new_message' => 'Новое сообщение - 11классники',
            'database_change' => 'Изменения в БД - 11классники'
        ];
        
        $subject = $subjects[$type] ?? 'Уведомление администратора - 11классники';
        $body = getAdminNotificationTemplate($type, $data);
        
        return $this->send($adminEmail, $subject, $body);
    }
    
    /**
     * Send user notification
     */
    public function sendUserNotification($email, $type, $data = []) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/user-notification.php';
        
        $subjects = [
            'welcome' => 'Добро пожаловать на 11классники',
            'account_suspended' => 'Аккаунт заблокирован - 11классники',
            'account_unsuspended' => 'Аккаунт разблокирован - 11классники',
            'comment_reply' => 'Ответ на комментарий - 11классники'
        ];
        
        $subject = $subjects[$type] ?? 'Уведомление - 11классники';
        $body = getUserNotificationTemplate($type, $data);
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Generic email sending with custom content
     */
    public function sendCustom($email, $subject, $title, $content, $buttonText = '', $buttonUrl = '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/base-template.php';
        
        $body = getBaseEmailTemplate($title, $content, $buttonText, $buttonUrl);
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Core email sending method
     */
    private function send($to, $subject, $body) {
        try {
            // Clear any previous recipients
            $this->mailer->clearAddresses();
            $this->mailer->clearReplyTos();
            
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            
            // Add plain text alternative
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));
            
            $this->mailer->send();
            
            // Log success (you can implement proper logging here)
            error_log("Email sent successfully to: {$to}, Subject: {$subject}");
            
            return true;
        } catch (Exception $e) {
            // Log error (you can implement proper logging here)
            error_log("Email failed to: {$to}, Subject: {$subject}, Error: {$this->mailer->ErrorInfo}");
            
            return false;
        }
    }
    
    /**
     * Send bulk emails (with rate limiting)
     */
    public function sendBulk($recipients, $subject, $title, $content, $buttonText = '', $buttonUrl = '') {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        foreach ($recipients as $recipient) {
            // Rate limiting - wait 100ms between emails
            usleep(100000);
            
            if ($this->sendCustom($recipient['email'], $subject, $title, $content, $buttonText, $buttonUrl)) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = $recipient['email'];
            }
        }
        
        return $results;
    }
}

// Helper function for backward compatibility
function getEmailService() {
    return EmailService::getInstance();
}
?>