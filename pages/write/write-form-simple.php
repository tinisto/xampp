<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userEmail = $_SESSION['email'] ?? '';
?>

<style>
    .write-page {
        padding: 40px 0;
    }
    .write-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 50px 0;
        margin-bottom: 50px;
        text-align: center;
    }
    .write-title {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 15px;
    }
    .write-subtitle {
        font-size: 18px;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }
    .contact-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }
    .contact-card {
        background: var(--card-bg, white);
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid var(--border-color, #e0e0e0);
    }
    .contact-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }
    .contact-icon {
        font-size: 36px;
        color: #667eea;
        margin-bottom: 20px;
    }
    .contact-card h3 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--text-primary, #333);
    }
    .contact-card p {
        color: var(--text-secondary, #666);
        margin-bottom: 20px;
        font-size: 14px;
    }
    .contact-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    .contact-link:hover {
        color: #764ba2;
    }
    .contact-form-section {
        background: var(--card-bg, white);
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 40px;
        border: 1px solid var(--border-color, #e0e0e0);
    }
    .form-title {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 30px;
        text-align: center;
        color: var(--text-primary, #333);
    }
    .modern-form .form-label {
        font-weight: 500;
        color: var(--text-primary, #333);
        margin-bottom: 8px;
    }
    .modern-form .form-control,
    .modern-form .form-select {
        border: 2px solid var(--border-color, #e0e0e0);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 16px;
        transition: all 0.3s ease;
        background-color: var(--bg-secondary, #f8f9fa);
        color: var(--text-primary, #333);
    }
    .modern-form .form-control:focus,
    .modern-form .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        outline: none;
    }
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 30px;
        font-weight: 500;
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    .btn-primary:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }
    .info-section {
        text-align: center;
        margin-top: 50px;
    }
    .info-section h3 {
        font-size: 20px;
        margin-bottom: 20px;
        color: var(--text-primary, #333);
    }
    .social-links-write {
        display: flex;
        justify-content: center;
        gap: 20px;
    }
    .social-link-write {
        width: 50px;
        height: 50px;
        background: var(--bg-secondary, #f8f9fa);
        border: 2px solid var(--border-color, #e0e0e0);
        color: var(--text-primary, #333);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 20px;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    .social-link-write:hover {
        background: #667eea;
        border-color: #667eea;
        color: white;
        transform: translateY(-3px);
    }
    
    /* Dark mode */
    [data-bs-theme="dark"] .contact-card,
    [data-bs-theme="dark"] .contact-form-section {
        background: #1a202c;
        border-color: rgba(255,255,255,0.1);
    }
    [data-bs-theme="dark"] .contact-card h3,
    [data-bs-theme="dark"] .form-title,
    [data-bs-theme="dark"] .info-section h3 {
        color: #f7fafc;
    }
    [data-bs-theme="dark"] .contact-card p {
        color: #a0aec0;
    }
    [data-bs-theme="dark"] .modern-form .form-control,
    [data-bs-theme="dark"] .modern-form .form-select {
        background-color: #2d3748;
        border-color: rgba(255,255,255,0.1);
        color: #f7fafc;
    }
    [data-bs-theme="dark"] .modern-form .form-label {
        color: #f7fafc;
    }
    [data-bs-theme="dark"] .social-link-write {
        background: #2d3748;
        border-color: rgba(255,255,255,0.1);
        color: #f7fafc;
    }
    
    /* Mobile styles */
    @media (max-width: 768px) {
        .write-hero {
            padding: 40px 0;
        }
        .write-title {
            font-size: 28px;
        }
        .contact-options {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .contact-card,
        .contact-form-section {
            padding: 20px;
        }
        .form-title {
            font-size: 24px;
        }
    }
</style>

<div class="write-page">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                

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
                            <?php 
                            // Simple CSRF protection
                            if (!isset($_SESSION['csrf_token'])) {
                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                            }
                            ?>
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
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
                                <button type="submit" class="btn btn-primary btn-lg" id="submitButton" disabled>
                                    <i class="fas fa-paper-plane me-2"></i> Отправить сообщение
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>


            </div>
        </div>
    </div>
</div>

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