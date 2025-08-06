<?php
// Check if user is logged in (session already started in write.php)
$isLoggedIn = isset($_SESSION['user_id']);
$userEmail = $_SESSION['email'] ?? '';
?>

<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-section-header.php';
renderPageSectionHeader([
    'title' => 'Напишите нам',
    'showSearch' => false
]);
?>

<div style="text-align: center; padding: 20px 0;">
    <p style="font-size: 1.1rem; color: var(--text-primary, #666);">Мы всегда рады обратной связи. Задайте вопрос, поделитесь историей или предложите сотрудничество.</p>
</div>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px; background: var(--background, #ffffff); color: var(--text-primary, #333);">
    
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
</div> <!-- Close container -->

<style>
    /* Alert styles matching news page */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    
    .alert-info {
        color: #31708f;
        background-color: #d9edf7;
        border-color: #bce8f1;
    }
    
    [data-theme="dark"] .alert-info {
        color: #9dd5f3;
        background-color: #1e3a5f;
        border-color: #2c5282;
    }
    
    /* Form section matching news content area */
    .contact-form-section {
        background: var(--surface, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .form-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 25px;
        text-align: center;
        color: var(--text-primary, #333);
    }
    
    .modern-form .form-label {
        font-weight: 500;
        color: var(--text-primary, #333);
        margin-bottom: 8px;
        display: block;
    }
    
    .modern-form .form-control,
    .modern-form .form-select {
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 16px;
        transition: all 0.3s ease;
        background-color: var(--background, #ffffff);
        color: var(--text-primary, #333);
        width: 100%;
        box-sizing: border-box;
        display: block;
    }
    
    .modern-form .form-control:focus,
    .modern-form .form-select:focus {
        border-color: var(--primary-color, #28a745);
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        outline: none;
    }
    
    .mb-4 {
        margin-bottom: 1.5rem;
    }
    
    .d-grid {
        display: grid;
    }
    
    .btn {
        transition: all 0.3s ease;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    
    .btn-primary {
        background-color: var(--primary-color, #28a745);
        border: none;
        padding: 12px 30px;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: var(--primary-hover, #218838);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }
    
    .btn-lg {
        font-size: 1.125rem;
    }
    
    .me-2 {
        margin-right: 0.5rem;
    }
    
    /* Dark mode specific */
    [data-theme="dark"] .contact-form-section,
    [data-bs-theme="dark"] .contact-form-section {
        background: var(--surface, #1e293b);
        border-color: var(--border-color, #4a5568);
    }
    
    [data-theme="dark"] .form-title,
    [data-bs-theme="dark"] .form-title {
        color: var(--text-primary, #f7fafc);
    }
    
    [data-theme="dark"] .modern-form .form-label,
    [data-bs-theme="dark"] .modern-form .form-label {
        color: var(--text-primary, #f7fafc);
    }
    
    [data-theme="dark"] .modern-form .form-control,
    [data-theme="dark"] .modern-form .form-select,
    [data-bs-theme="dark"] .modern-form .form-control,
    [data-bs-theme="dark"] .modern-form .form-select {
        background-color: var(--surface, #1e293b);
        border-color: var(--border-color, #4a5568);
        color: var(--text-primary, #f7fafc);
    }
    
    /* Mobile styles */
    @media (max-width: 768px) {
        .contact-form-section {
            padding: 20px;
        }
        .form-title {
            font-size: 20px;
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