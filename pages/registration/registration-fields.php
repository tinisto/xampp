<?php
$oldData = $_SESSION['oldData'] ?? [];
unset($_SESSION['oldData']);
?>
<div class="mb-3">
    <input type="text" id="firstname" name="firstname" class="form-control" 
           value="<?= isset($oldData['firstname']) ? htmlspecialchars($oldData['firstname']) : '' ?>"
           placeholder="Имя" required>
</div>

<div class="mb-3">
    <input type="text" id="lastname" name="lastname" class="form-control" 
           value="<?= isset($oldData['lastname']) ? htmlspecialchars($oldData['lastname']) : '' ?>"
           placeholder="Фамилия" required>
</div>

<div class="mb-3">
    <select name="occupation" id="occupation" class="form-select" required>
        <option value="">Выберите род деятельности</option>
        <option value="Представитель ВУЗа" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель ВУЗа' ? 'selected' : '' ?>>Представитель ВУЗа</option>
        <option value="Представитель ССУЗа" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель ССУЗа' ? 'selected' : '' ?>>Представитель ССУЗа</option>
        <option value="Представитель школы" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель школы' ? 'selected' : '' ?>>Представитель школы</option>
        <option value="Родитель" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Родитель' ? 'selected' : '' ?>>Родитель</option>
        <option value="Учащийся/учащаяся" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Учащийся/учащаяся' ? 'selected' : '' ?>>Учащийся</option>
        <option value="Другое" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Другое' ? 'selected' : '' ?>>Другое</option>
    </select>
</div>

<div class="mb-3">
    <input type="email" id="email" name="email" class="form-control" 
           value="<?= isset($oldData['email']) ? htmlspecialchars($oldData['email']) : '' ?>"
           placeholder="Email адрес" required>
</div>

<div class="mb-3">
    <div class="input-group">
        <input type="password" id="newPassword" name="newPassword" class="form-control" placeholder="Пароль" required>
        <span class="input-group-text" id="togglePassword1">
            <i class="fa fa-eye" id="toggleIcon1"></i>
        </span>
    </div>
</div>

<div class="mb-3">
    <div class="input-group">
        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Подтвердите пароль" required>
        <span class="input-group-text" id="togglePassword2">
            <i class="fa fa-eye" id="toggleIcon2"></i>
        </span>
    </div>
</div>

<div class="mb-3">
    <label for="avatar" class="form-label">Аватар (необязательно)</label>
    <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
</div>

<input type="hidden" name="timezone" id="timezone" value="">

<script>
// Timezone detection
function detectTimezone() {
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.getElementById('timezone').value = timezone;
}
window.addEventListener('load', detectTimezone);

// Password visibility toggles
document.addEventListener('DOMContentLoaded', function() {
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
    
    setupPasswordToggle('togglePassword1', 'newPassword', 'toggleIcon1');
    setupPasswordToggle('togglePassword2', 'confirmPassword', 'toggleIcon2');
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        if (newPassword !== confirmPassword) {
            alert('Пароли не совпадают');
            event.preventDefault();
            return false;
        }
        
        if (newPassword.length < 8) {
            alert('Пароль должен содержать минимум 8 символов');
            event.preventDefault();
            return false;
        }
    });
});
</script>