<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userEmail = $_SESSION['email'] ?? '';
?>

<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 0; text-align: center; margin-bottom: 40px;">
    <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 15px;">Напишите нам</h1>
    <p style="font-size: 1.125rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">Мы всегда рады обратной связи</p>
</div>

<div style="max-width: 800px; margin: 0 auto; padding: 40px 20px;">
            
            <!-- Contact Form -->
            <div class="contact-form-section">
                <h2 class="form-title">Отправить сообщение</h2>
                
                <?php if (!$isLoggedIn): ?>
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        Чтобы отправить сообщение, пожалуйста, <a href="/login">войдите</a> или <a href="/registration">зарегистрируйтесь</a>.
                    </div>
                <?php else: ?>
                    <form id="messageForm" action="/pages/write/write-process-form.php" method="post" class="modern-form">
                        <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/csrf.php'; ?>
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-4">
                            <label for="subject" class="form-label">Тема сообщения</label>
                            <select class="form-select" id="subject" name="subject" required>
                                <option value="">Выберите тему...</option>
                                <option value="question">Общий вопрос</option>
                                <option value="story">Хочу рассказать свою историю</option>
                                <option value="feedback">Отзыв о сайте</option>
                                <option value="partnership">Сотрудничество</option>
                                <option value="technical">Техническая проблема</option>
                                <option value="other">Другое</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="message" class="form-label">Ваше сообщение</label>
                            <textarea class="form-control" id="message" name="message" rows="6" placeholder="Расскажите подробнее..." required></textarea>
                        </div>
                        
                        <input type="hidden" name="userEmail" value="<?php echo htmlspecialchars($userEmail); ?>">
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitButton">
                                <i class="fas fa-paper-plane me-2"></i> Отправить сообщение
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>


</div>

<?php include \$_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>

<style>
    .contact-form-section {
        background: var(--color-surface-primary, #ffffff);
        border: 1px solid var(--color-border, #e0e0e0);
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }
    .form-title {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 30px;
        text-align: center;
        color: var(--color-text-primary, #333);
    }
    .modern-form .form-label {
        font-weight: 500;
        color: var(--color-text-primary, #333);
        margin-bottom: 8px;
        display: block;
    }
    .modern-form .form-control,
    .modern-form .form-select {
        border: 2px solid #ddd !important;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 16px;
        transition: all 0.3s ease;
        background-color: #ffffff !important;
        color: #333 !important;
        width: 100%;
        box-sizing: border-box;
        display: block !important;
        visibility: visible !important;
    }
    .modern-form .form-control:focus,
    .modern-form .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 30px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }
    
    /* Dark mode specific */
    [data-theme="dark"] .contact-form-section,
    [data-bs-theme="dark"] .contact-form-section {
        background: var(--color-surface-primary, #1f2937);
        border-color: var(--color-border, #374151);
    }
    [data-theme="dark"] .form-title,
    [data-bs-theme="dark"] .form-title {
        color: var(--color-text-primary, #f9fafb);
    }
    [data-theme="dark"] .modern-form .form-label,
    [data-bs-theme="dark"] .modern-form .form-label {
        color: var(--color-text-primary, #f9fafb);
    }
    [data-theme="dark"] .modern-form .form-control,
    [data-theme="dark"] .modern-form .form-select,
    [data-bs-theme="dark"] .modern-form .form-control,
    [data-bs-theme="dark"] .modern-form .form-select {
        background-color: var(--color-surface-primary, #1f2937);
        border-color: var(--color-border, #374151);
        color: var(--color-text-primary, #f9fafb);
    }
    
    /* Mobile styles */
    @media (max-width: 768px) {
        .contact-form-section {
            padding: 20px;
            box-shadow: none;
            border-radius: 0;
        }
        .form-title {
            font-size: 24px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('messageForm');
    if (form) {
        var submitButton = document.getElementById('submitButton');
        var messageTextarea = document.getElementById('message');
        var subjectSelect = document.getElementById('subject');
        
        function checkFormValidity() {
            var isValid = messageTextarea.value.trim().length > 0 && subjectSelect.value !== '';
            submitButton.disabled = !isValid;
        }
        
        messageTextarea.addEventListener('input', checkFormValidity);
        subjectSelect.addEventListener('change', checkFormValidity);
        
        checkFormValidity();
    }
});
</script>