<?php
session_start();
require_once "config/loadEnv.php";
require_once "database/db_connections.php";

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$oldData = $_SESSION['oldData'] ?? [];
unset($_SESSION['oldData']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - 11-классники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .registration-container {
            max-width: 500px;
            margin: 50px auto;
        }
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #28a745;
            color: white;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="registration-container">
            <div class="card">
                <div class="card-header">
                    Регистрация
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <p class="mb-1"><?= htmlspecialchars($error) ?></p>
                            <?php endforeach; ?>
                            <?php unset($_SESSION['errors']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="/pages/registration/registration_process_simple.php" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <div class="mb-3">
                            <label for="firstname" class="form-label">Имя</label>
                            <input type="text" id="firstname" name="firstname" class="form-control" 
                                   value="<?= isset($oldData['firstname']) ? htmlspecialchars($oldData['firstname']) : '' ?>"
                                   placeholder="Ваше имя" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Фамилия</label>
                            <input type="text" id="lastname" name="lastname" class="form-control" 
                                   value="<?= isset($oldData['lastname']) ? htmlspecialchars($oldData['lastname']) : '' ?>"
                                   placeholder="Ваша фамилия" required>
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
                            <label for="email" class="form-label">Email адрес</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?= isset($oldData['email']) ? htmlspecialchars($oldData['email']) : '' ?>"
                                   placeholder="your@email.com" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Пароль</label>
                            <div class="input-group">
                                <input type="password" id="newPassword" name="newPassword" class="form-control" 
                                       placeholder="Минимум 8 символов" required>
                                <span class="input-group-text" id="togglePassword1" style="cursor: pointer;">
                                    <i class="fa fa-eye" id="toggleIcon1"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Подтвердите пароль</label>
                            <div class="input-group">
                                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" 
                                       placeholder="Повторите пароль" required>
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
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Зарегистрироваться</button>
                    </form>
                    
                    <div class="text-center">
                        <p>Уже есть аккаунт? <a href="/login">Войдите здесь</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
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
            const toggle = document.getElementById(toggleId);
            if (toggle) {
                toggle.addEventListener('click', function() {
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
</body>
</html>