<?php
/**
 * Secure Login Form with modern security features
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/security/security_bootstrap.php';

// Set security headers
SecurityHeaders::preventCaching(); // Don't cache login page

// Configuration for the secure form
$formConfig = [
    'title' => 'Вход',
    'action' => '/pages/login/login_process_secure.php',
    'submitText' => 'Войти',
    'bottomLink' => [
        'text' => 'Нет аккаунта?',
        'url' => '/registration',
        'linkText' => 'Зарегистрироваться'
    ]
];

// Get current theme
$currentTheme = $_COOKIE['preferred-theme'] ?? 'light';

// Check for rate limiting
$rateLimited = RateLimiter::isRateLimited('login_page_view', 20, 300); // 20 views per 5 minutes
if ($rateLimited) {
    $remaining = RateLimiter::getTimeRemaining('login_page_view', 300);
    $errorMessage = "Слишком много попыток доступа. Попробуйте через " . ceil($remaining / 60) . " минут.";
} else {
    RateLimiter::recordAttempt('login_page_view');
}
?>

<!DOCTYPE html>
<html lang="ru" data-theme="<?= SecurityBootstrap::out($currentTheme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SecurityBootstrap::out($formConfig['title']) ?> - 11-классники</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    
    <!-- CSRF token for AJAX requests -->
    <?= CSRFProtection::getMetaTag() ?>
    
    <!-- Theme Variables -->
    <link rel="stylesheet" href="/css/theme-variables.css">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Login Fix CSS -->
    <link rel="stylesheet" href="/css/login-fix.css">
    
    <style>
        /* Secure form styles */
        body {
            background-color: var(--color-surface-primary);
            color: var(--color-text-primary);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        .form-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        
        .form-wrapper {
            width: 100%;
            max-width: 500px;
        }
        
        .form-card {
            background-color: var(--color-card-bg);
            border: 1px solid var(--color-border-primary);
            border-radius: 0.375rem;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem var(--color-shadow-sm);
        }
        
        .form-title {
            color: var(--color-primary);
            font-weight: 600;
            font-size: 1.875rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            background-color: var(--color-input-bg);
            border: 1px solid var(--color-border-primary);
            color: var(--color-text-primary);
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .form-control:focus {
            background-color: var(--color-input-bg);
            border-color: var(--color-primary);
            color: var(--color-text-primary);
            box-shadow: 0 0 0 0.2rem var(--color-primary-alpha);
        }
        
        .btn-primary {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            border-radius: 0.375rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            width: 100%;
        }
        
        .btn-primary:hover {
            background-color: var(--color-primary-hover);
            border-color: var(--color-primary-hover);
        }
        
        .alert {
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        
        .alert-danger {
            background-color: var(--color-danger-bg);
            border-color: var(--color-danger);
            color: var(--color-danger);
        }
        
        .alert-success {
            background-color: var(--color-success-bg);
            border-color: var(--color-success);
            color: var(--color-success);
        }
        
        .text-center a {
            color: var(--color-primary);
            text-decoration: none;
        }
        
        .text-center a:hover {
            text-decoration: underline;
        }
        
        .security-notice {
            font-size: 0.875rem;
            color: var(--color-text-secondary);
            text-align: center;
            margin-top: 1rem;
            padding: 0.5rem;
            background-color: var(--color-surface-secondary);
            border-radius: 0.25rem;
        }
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }
        
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background-color: var(--color-border-primary);
            margin-top: 0.25rem;
        }
        
        .strength-fill {
            height: 100%;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-wrapper">
            <div class="form-card">
                <h1 class="form-title"><?= SecurityBootstrap::out($formConfig['title']) ?></h1>
                
                <?php if (isset($errorMessage)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= SecurityBootstrap::out($errorMessage) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= SecurityBootstrap::out($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= SecurityBootstrap::out($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (!$rateLimited): ?>
                    <form method="POST" action="<?= SecurityBootstrap::out($formConfig['action']) ?>" id="loginForm">
                        <!-- CSRF Protection -->
                        <?= CSRFProtection::getTokenField('login') ?>
                        
                        <div class="mb-3">
                            <input type="email" 
                                   name="email" 
                                   class="form-control" 
                                   placeholder="Email адрес" 
                                   required 
                                   autofocus
                                   autocomplete="username"
                                   maxlength="255">
                        </div>
                        
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="password" 
                                       name="password" 
                                       class="form-control" 
                                       id="passwordInput" 
                                       placeholder="Пароль" 
                                       required
                                       autocomplete="current-password"
                                       maxlength="255">
                                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                    <i class="fa fa-eye" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Запомнить меня (30 дней)
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            <?= SecurityBootstrap::out($formConfig['submitText']) ?>
                        </button>
                    </form>
                    
                    <?php if ($formConfig['bottomLink']): ?>
                        <div class="text-center mt-3">
                            <?= SecurityBootstrap::out($formConfig['bottomLink']['text']) ?>
                            <a href="<?= SecurityBootstrap::out($formConfig['bottomLink']['url']) ?>">
                                <?= SecurityBootstrap::out($formConfig['bottomLink']['linkText']) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-3">
                        <a href="/forgot-password">Забыли пароль?</a>
                    </div>
                    
                    <div class="security-notice">
                        <i class="fas fa-shield-alt me-1"></i>
                        Ваши данные защищены современными методами шифрования
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const passwordInput = document.getElementById('passwordInput');
                    const toggleIcon = document.getElementById('toggleIcon');
                    
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    }
                });
            }
            
            // Form submission with loading state
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            
            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Вход...';
                });
            }
            
            // Auto-focus on first error field
            const errorField = document.querySelector('.is-invalid');
            if (errorField) {
                errorField.focus();
            }
        });
    </script>
</body>
</html>