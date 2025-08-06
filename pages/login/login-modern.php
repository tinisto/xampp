<?php
// Start session for error handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the ultimate template engine
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

// Create login form content
$loginContent = '
<div class="container" style="max-width: 500px; margin-top: 60px;">
    <div class="card">
        <div class="card-body p-4">
            <h1 class="text-center mb-4" style="color: var(--color-primary);">Вход</h1>
            
            <?php if (isset($_SESSION["error"])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION["error"]) ?>
                    <?php unset($_SESSION["error"]); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION["success"])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION["success"]) ?>
                    <?php unset($_SESSION["success"]); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="/pages/login/login_process_simple.php">
                <?php if (!isset($_SESSION["csrf_token"])) {
                    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
                } ?>
                <input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>">
                
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email адрес" required autofocus>
                </div>
                
                <div class="mb-3">
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Пароль" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fa fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Войти</button>
            </form>
            
            <p class="text-center mt-3">
                Нет аккаунта? 
                <a href="/registration" style="color: var(--color-link);">Зарегистрироваться</a>
            </p>
        </div>
    </div>
</div>

<style>
/* Additional form styles using CSS variables */
.form-control {
    background-color: var(--color-surface-secondary);
    color: var(--color-text-primary);
    border-color: var(--color-border-primary);
}

.form-control:focus {
    background-color: var(--color-surface-primary);
    color: var(--color-text-primary);
    border-color: var(--color-primary);
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.1);
}

.form-control::placeholder {
    color: var(--color-text-tertiary);
    opacity: 1;
}

.input-group .btn-outline-secondary {
    background-color: var(--color-surface-secondary);
    color: var(--color-text-secondary);
    border-color: var(--color-border-primary);
}

.input-group .btn-outline-secondary:hover {
    background-color: var(--color-bg-hover);
    color: var(--color-text-primary);
}

.alert {
    border-radius: 8px;
}

.alert-danger {
    background-color: rgba(220, 53, 69, 0.1);
    border-color: var(--color-danger);
    color: var(--color-danger);
}

.alert-success {
    background-color: rgba(40, 167, 69, 0.1);
    border-color: var(--color-success);
    color: var(--color-success);
}

/* Dark mode specific */
[data-theme="dark"] .alert-danger {
    background-color: rgba(248, 113, 113, 0.1);
}

[data-theme="dark"] .alert-success {
    background-color: rgba(74, 222, 128, 0.1);
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("togglePassword").addEventListener("click", function() {
        const passwordInput = document.getElementById("passwordInput");
        const toggleIcon = document.getElementById("toggleIcon");
        
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    });
});
</script>
';

// Save the content
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/pages/login/login-modern-content.php', $loginContent);

// Render the page
$templateConfig = [
    'layoutType' => 'default',
    'darkMode' => true,
];

renderTemplate('Вход', 'pages/login/login-modern-content.php', $templateConfig);
?>