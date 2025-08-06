<?php
// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userEmail = $_SESSION['email'] ?? '';
?>

<div style="text-align: center; padding: 20px 0;">
    <p style="font-size: 1.1rem; color: var(--text-primary, #666);">Мы всегда рады обратной связи</p>
</div>

<div class="form-container">
    <div class="contact-form-section">
        <h2 class="form-title">Отправить сообщение</h2>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                Спасибо за ваше сообщение! Мы ответим вам в ближайшее время.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Произошла ошибка при отправке сообщения. Попробуйте еще раз.
            </div>
        <?php endif; ?>
        
        <form method="post" action="/pages/write/write-process.php" class="contact-form">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label for="name">Имя *</label>
                <input type="text" id="name" name="name" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($userEmail) ?>" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="subject">Тема</label>
                <input type="text" id="subject" name="subject" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="message">Сообщение *</label>
                <textarea id="message" name="message" rows="6" required class="form-control"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>
                Отправить сообщение
            </button>
        </form>
    </div>
</div>

<style>
.form-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
}

.contact-form-section {
    background: var(--surface, white);
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.form-title {
    color: var(--text-primary, #333);
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 30px;
    text-align: center;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: var(--text-primary, #333);
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border-color, #e1e5e9);
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: var(--surface, white);
    color: var(--text-primary, #333);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color, #28a745);
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 14px 32px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-primary {
    background: var(--primary-color, #28a745);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-hover, #218838);
    transform: translateY(-1px);
}

.alert {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
}

.alert-success {
    background: rgba(40, 167, 69, 0.1);
    color: var(--success-color, #28a745);
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.alert-danger {
    background: rgba(220, 53, 69, 0.1);
    color: var(--danger-color, #dc3545);
    border: 1px solid rgba(220, 53, 69, 0.2);
}

@media (max-width: 768px) {
    .contact-form-section {
        padding: 30px 20px;
    }
    
    .form-title {
        font-size: 1.5rem;
    }
}
</style>