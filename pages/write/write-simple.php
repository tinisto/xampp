<?php
// Simple write page without dependencies
session_start();

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userEmail = $_SESSION['email'] ?? '';

$pageTitle = 'Напишите нам';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
    <link href="/css/unified-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .write-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .write-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .write-hero p {
            font-size: 1.125rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .form-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 30px;
            color: #333;
        }
        
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .btn {
            display: inline-block;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            padding: 12px 30px;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 8px;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            color: #fff;
            background-color: #007bff;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        /* Dark mode fixes */
        [data-bs-theme="dark"] .btn-primary,
        [data-theme="dark"] .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
        
        [data-bs-theme="dark"] .btn-primary:hover,
        [data-theme="dark"] .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            color: #fff;
        }
        
        /* Ensure button text is always visible */
        .btn-primary {
            color: #fff !important;
        }
        
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <?php 
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-section-header.php';
    renderPageSectionHeader([
        'title' => 'Напишите нам',
        'showSearch' => false
    ]);
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
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Произошла ошибка при отправке сообщения. Пожалуйста, попробуйте еще раз.
                </div>
            <?php endif; ?>
            
            <?php if (!$isLoggedIn): ?>
                <div class="alert alert-info">
                    Чтобы отправить сообщение, пожалуйста,&nbsp;<a href="/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">войдите</a>.
                </div>
            <?php else: ?>
                <form action="/pages/write/write-process-form.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($userEmail) ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject" class="form-label">Тема сообщения</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">Сообщение</label>
                        <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-paper-plane me-2"></i> Отправить сообщение
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
    </div>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
</body>
</html>