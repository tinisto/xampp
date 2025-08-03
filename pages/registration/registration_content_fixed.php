<?php
require_once __DIR__ . '/../../includes/init.php';

// Retrieve errors and old data
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$oldData = isset($_SESSION['oldData']) ? $_SESSION['oldData'] : [];

// Clear session variables after retrieving them
unset($_SESSION['errors']);
unset($_SESSION['oldData']);
?>

<section class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-lg-6 col-md-8 col-sm-10 mx-auto">
        <div class="border border-dark-subtle rounded-3 mb-2 pb-2 px-2">
            <div class="p-3 shadow-sm">
                <h5 class="text-center mb-3 sign-up-custom">Регистрация</h5>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p class="mb-1"><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form id="registrationForm" method="post" action="/pages/registration/registration_process.php" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstname" class="form-label">Имя</label>
                            <input type="text" id="firstname" name="firstname" class="form-control"
                                value="<?= isset($oldData['firstname']) ? htmlspecialchars($oldData['firstname']) : '' ?>"
                                placeholder="Введите ваше имя" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastname" class="form-label">Фамилия</label>
                            <input type="text" id="lastname" name="lastname" class="form-control"
                                value="<?= isset($oldData['lastname']) ? htmlspecialchars($oldData['lastname']) : '' ?>"
                                placeholder="Введите вашу фамилию" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="occupation" class="form-label">Род деятельности</label>
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
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                            value="<?= isset($oldData['email']) ? htmlspecialchars($oldData['email']) : '' ?>"
                            placeholder="Введите ваш email" required>
                    </div>

                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Пароль</label>
                        <div class="input-group">
                            <input type="password" id="newPassword" name="newPassword" class="form-control" placeholder="Введите ваш пароль" required>
                            <span class="input-group-text" id="togglePassword1" style="cursor: pointer;">
                                <i class="fa fa-eye" id="toggleIcon1"></i>
                            </span>
                        </div>
                        <div class="form-text">Пароль должен содержать минимум 8 символов и включать буквы и цифры.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Подтвердите пароль</label>
                        <div class="input-group">
                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Подтвердите пароль" required>
                            <span class="input-group-text" id="togglePassword2" style="cursor: pointer;">
                                <i class="fa fa-eye" id="toggleIcon2"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="avatar" class="form-label">Аватар (необязательно)</label>
                        <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                    </div>

                    <input type="hidden" name="timezone" id="timezone" value="">

                    <button type="submit" class="btn btn-success d-block mx-auto mt-3">
                        <span class="fw-bold">Зарегистрироваться</span>
                    </button>
                </form>
                
                <p class="mt-3 text-center">Уже есть аккаунт? <a href="/login" class="text-decoration-none">Войдите здесь</a>.</p>
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
// Timezone detection
function detectTimezone() {
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.getElementById('timezone').value = timezone;
}
window.addEventListener('load', detectTimezone);

// Password visibility toggles
function setupPasswordToggle(passwordId, toggleId, iconId) {
    document.getElementById(toggleId).addEventListener('click', function() {
        const passwordInput = document.getElementById(passwordId);
        const toggleIcon = document.getElementById(iconId);
        
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

// Setup both password toggles
setupPasswordToggle('newPassword', 'togglePassword1', 'toggleIcon1');
setupPasswordToggle('confirmPassword', 'togglePassword2', 'toggleIcon2');

// Form validation
document.getElementById('registrationForm').addEventListener('submit', function(event) {
    const firstname = document.getElementById('firstname').value.trim();
    const lastname = document.getElementById('lastname').value.trim();
    const email = document.getElementById('email').value.trim();
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const occupation = document.getElementById('occupation').value;

    // Check required fields
    if (!firstname || !lastname || !email || !newPassword || !confirmPassword || !occupation) {
        alert("Пожалуйста, заполните все обязательные поля.");
        event.preventDefault();
        return false;
    }
    
    // Check passwords match
    if (newPassword !== confirmPassword) {
        alert("Пароли не совпадают.");
        event.preventDefault();
        return false;
    }

    // Check password strength
    if (newPassword.length < 8) {
        alert("Пароль должен содержать минимум 8 символов.");
        event.preventDefault();
        return false;
    }

    return true;
});
</script>