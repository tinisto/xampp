<?php
require_once __DIR__ . '/../../includes/init.php';
?>
<section class="d-flex justify-content-center align-items-center" style="height: 60vh;">
    <div class="col-lg-4 col-md-6 col-sm-10 mx-auto">
        <div class="border border-dark-subtle rounded-3 mb-2 pb-2 px-2">
            <div class="p-3 shadow-sm">
                <div class="text-center mb-3">
                    <a href="/" class="text-decoration-none">
                        <i class="fas fa-home text-success me-2"></i>
                        <span class="text-muted small">Вернуться на главную</span>
                    </a>
                </div>
                <h5 class="text-center mb-3 sign-up-custom">Логин</h5>
                <p class="text-center mb-3">
                    <small class="text-muted">Нет аккаунта? <a href="/registration" class="text-success">Зарегистрироваться</a></small>
                </p>
                <form method="post" action="/login/login_process.php">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3 mx-2">
                        <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Email адрес" required>
                    </div>
                    <div class="mx-2 mb-3">
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Пароль" required>
                            <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                <i class="fa fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success d-block mx-auto mt-3">Логин</button>
                </form>
                
                <div class="text-center my-3">
                    <small class="text-muted">или войдите через социальные сети</small>
                </div>
                
                <div class="d-flex gap-2 justify-content-center">
                    <a href="/auth/vk" class="btn btn-primary btn-sm flex-fill" style="background: #4c75a3; border-color: #4c75a3;">
                        <i class="fab fa-vk me-1"></i> VK
                    </a>
                    <a href="/auth/google" class="btn btn-danger btn-sm flex-fill">
                        <i class="fab fa-google me-1"></i> Google
                    </a>
                    <a href="/auth/yandex" class="btn btn-warning btn-sm flex-fill" style="color: white;">
                        <i class="fab fa-yandex me-1"></i> Яндекс
                    </a>
                </div>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger mt-3">
                        <?= h($_SESSION['error']) ?>
                        
                        <?php if (strpos($_SESSION['error'], 'не активирован') !== false): ?>
                            <hr>
                            <p class="mb-2"><strong>Проблемы с активацией?</strong></p>
                            <div class="d-grid gap-2">
                                <a href="/pages/registration/resend_activation/resend_activation.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-envelope me-1"></i> Отправить код активации повторно
                                </a>
                                <a href="/activate-user-manual.php" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-tools me-1"></i> Активировать аккаунт вручную
                                </a>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                Если проблема не решается, свяжитесь с поддержкой: 
                                <a href="mailto:support@11klassniki.ru">support@11klassniki.ru</a>
                            </small>
                        <?php endif; ?>
                        
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['registration_success']) && isset($_GET['message'])): ?>
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($_GET['message']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
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
</script>