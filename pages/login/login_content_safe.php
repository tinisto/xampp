<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<section class="d-flex justify-content-center align-items-center" style="height: 60vh;">
    <div class="col-lg-4 col-md-6 col-sm-10 mx-auto">
        <div class="border border-dark-subtle rounded-3 mb-2 pb-2 px-2">
            <div class="p-3 shadow-sm">
                <h5 class="text-center mb-3 sign-up-custom" style="color: #28a745;">Вход</h5>
                <form method="post" action="/login/login_process.php">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
                    <button type="submit" class="btn btn-success d-block mx-auto mt-3">Войти</button>
                </form>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger mt-3">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <p class="mt-3 text-center">Нет аккаунта? <a href="/registration" style="color: #28a745; text-decoration: none;">Зарегистрироваться</a></p>
            </div>
        </div>
    </div>
</section>

<style>
.input-group > .form-control {
    border-right: none;
}
.input-group-text {
    background: #ffffff;
    border-left: none;
}
</style>

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