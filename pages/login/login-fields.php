<div class="mb-3">
    <input type="email" name="email" class="form-control" placeholder="Email адрес" required autofocus>
</div>

<div class="mb-3">
    <div class="input-group">
        <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Пароль" required>
        <span class="input-group-text" id="togglePassword">
            <i class="fa fa-eye" id="toggleIcon"></i>
        </span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>