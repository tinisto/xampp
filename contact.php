<?php
// Contact form page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Handle form submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Неверный формат email адреса';
    } else {
        // Save to database
        try {
            $db = Database::getInstance();
            
            // Get client information
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            // Save message to database
            $messageId = $db->insert('contact_messages', [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);
            
            if ($messageId) {
                $success = true;
            } else {
                $error = 'Не удалось сохранить сообщение. Попробуйте позже.';
            }
        } catch (Exception $e) {
            $error = 'Произошла ошибка при отправке сообщения. Попробуйте позже.';
        }
    }
}

// Page title
$pageTitle = 'Связь с нами';

// Section 1: Header - Airbnb style with bold typography
ob_start();
?>
<div style="padding: 20px 20px 20px; background: white; box-shadow: 0 1px 0 rgba(0,0,0,0.08);">
    <div style="max-width: 520px; margin: 0 auto; text-align: center;">
        <h1 style="font-size: 44px; font-weight: 800; margin-bottom: 16px; color: #222222; letter-spacing: -0.02em;">
            Как мы можем помочь?
        </h1>
        <p style="font-size: 18px; color: #717171; line-height: 1.5;">
            Наша команда готова ответить на ваши вопросы. Заполните форму, и мы свяжемся с вами как можно скорее.
        </p>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Contact form
ob_start();
?>
<div style="padding: 30px 20px; background: white;">
    <div style="max-width: 600px; margin: 0 auto;">
        
        <?php if ($success): ?>
        <div style="padding: 20px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; color: #155724; margin-bottom: 30px;">
            <i class="fas fa-check-circle"></i> <strong>Спасибо!</strong> Ваше сообщение отправлено. Мы свяжемся с Вами в ближайшее время.
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div style="padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; color: #721c24; margin-bottom: 30px;">
            <i class="fas fa-exclamation-triangle"></i> <strong>Ошибка:</strong> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="post" style="display: flex; flex-direction: column; gap: 20px;">
            <div>
                <label for="name" style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">
                    Имя *
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       required
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       style="width: 100%; padding: 12px 16px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; background: white; color: #333;"
                       placeholder="Введите ваше имя">
            </div>
            
            <div>
                <label for="email" style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">
                    Email *
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       style="width: 100%; padding: 12px 16px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; background: white; color: #333;"
                       placeholder="your@email.com">
            </div>
            
            <div>
                <label for="subject" style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">
                    Тема *
                </label>
                <select id="subject" 
                        name="subject" 
                        required
                        style="width: 100%; padding: 12px 16px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; background: white; color: #333;">
                    <option value="">Выберите тему</option>
                    <option value="general" <?= ($_POST['subject'] ?? '') === 'general' ? 'selected' : '' ?>>Общий вопрос</option>
                    <option value="technical" <?= ($_POST['subject'] ?? '') === 'technical' ? 'selected' : '' ?>>Техническая поддержка</option>
                    <option value="content" <?= ($_POST['subject'] ?? '') === 'content' ? 'selected' : '' ?>>Вопрос по контенту</option>
                    <option value="cooperation" <?= ($_POST['subject'] ?? '') === 'cooperation' ? 'selected' : '' ?>>Сотрудничество</option>
                    <option value="privacy" <?= ($_POST['subject'] ?? '') === 'privacy' ? 'selected' : '' ?>>Конфиденциальность</option>
                    <option value="other" <?= ($_POST['subject'] ?? '') === 'other' ? 'selected' : '' ?>>Другое</option>
                </select>
            </div>
            
            <div>
                <label for="message" style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">
                    Сообщение *
                </label>
                <textarea id="message" 
                          name="message" 
                          required
                          rows="6"
                          style="width: 100%; padding: 12px 16px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; font-family: inherit; resize: vertical; background: white; color: #333;"
                          placeholder="Расскажите подробнее о вашем вопросе или предложении..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" 
                    style="padding: 15px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <i class="fas fa-paper-plane"></i> Отправить сообщение
            </button>
        </form>
        
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Additional info
ob_start();
?>
<div style="padding: 30px 20px; background: #f8f9fa;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            
            <div style="text-align: center; padding: 30px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-clock" style="color: white; font-size: 24px;"></i>
                </div>
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #333;">Время ответа</h3>
                <p style="color: #666; margin: 0;">
                    Мы отвечаем на сообщения в течение 1-2 рабочих дней
                </p>
            </div>
            
            <div style="text-align: center; padding: 30px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-shield-alt" style="color: white; font-size: 24px;"></i>
                </div>
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #333;">Конфиденциальность</h3>
                <p style="color: #666; margin: 0;">
                    Ваши данные защищены и не передаются третьим лицам
                </p>
            </div>
            
            <div style="text-align: center; padding: 30px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-users" style="color: white; font-size: 24px;"></i>
                </div>
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #333;">Поддержка</h3>
                <p style="color: #666; margin: 0;">
                    Наша команда готова помочь вам с любыми вопросами
                </p>
            </div>
            
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Other sections empty
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';
$blueContent = '';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>