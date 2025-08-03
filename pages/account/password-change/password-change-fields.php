<form action="/pages/account/password-change/password-change-process-simple.php" method="post">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    
    <div class="mb-3">
        <label for="current_password" class="form-label">Текущий пароль</label>
        <div class="input-group">
            <input type="password" class="form-control" id="current_password" name="current_password" required>
            <span class="input-group-text" id="toggleCurrent" style="cursor: pointer;">
                <i class="fa fa-eye" id="toggleIconCurrent"></i>
            </span>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="new_password" class="form-label">Новый пароль</label>
        <div class="input-group">
            <input type="password" class="form-control" id="new_password" name="new_password" required>
            <span class="input-group-text" id="toggleNew" style="cursor: pointer;">
                <i class="fa fa-eye" id="toggleIconNew"></i>
            </span>
        </div>
        <div class="form-text">Минимум 8 символов, должен содержать буквы и цифры</div>
    </div>
    
    <div class="mb-3">
        <label for="confirm_password" class="form-label">Подтвердите новый пароль</label>
        <div class="input-group">
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            <span class="input-group-text" id="toggleConfirm" style="cursor: pointer;">
                <i class="fa fa-eye" id="toggleIconConfirm"></i>
            </span>
        </div>
    </div>
    
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-key"></i> Изменить пароль
        </button>
    </div>
</form>

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
function setupPasswordToggle(toggleId, inputId, iconId) {
    document.getElementById(toggleId).addEventListener('click', function() {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    setupPasswordToggle('toggleCurrent', 'current_password', 'toggleIconCurrent');
    setupPasswordToggle('toggleNew', 'new_password', 'toggleIconNew');
    setupPasswordToggle('toggleConfirm', 'confirm_password', 'toggleIconConfirm');
});
</script>