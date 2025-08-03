<?php
// Simple standalone registration page to test
session_start();

// Generate CSRF token if needed
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
    <title>Регистрация - Тест</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #28a745;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #28a745;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .link {
            text-align: center;
            margin-top: 20px;
        }
        .link a {
            color: #28a745;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Регистрация</h1>
        
        <?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
            <div class="error">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p style="margin: 5px 0;"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="/pages/registration/registration_process_simple.php" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <input type="text" name="firstname" placeholder="Имя" 
                       value="<?= isset($oldData['firstname']) ? htmlspecialchars($oldData['firstname']) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <input type="text" name="lastname" placeholder="Фамилия" 
                       value="<?= isset($oldData['lastname']) ? htmlspecialchars($oldData['lastname']) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <select name="occupation" required>
                    <option value="">Выберите род деятельности</option>
                    <option value="Представитель ВУЗа" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель ВУЗа' ? 'selected' : '' ?>>Представитель ВУЗа</option>
                    <option value="Представитель ССУЗа" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель ССУЗа' ? 'selected' : '' ?>>Представитель ССУЗа</option>
                    <option value="Представитель школы" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель школы' ? 'selected' : '' ?>>Представитель школы</option>
                    <option value="Родитель" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Родитель' ? 'selected' : '' ?>>Родитель</option>
                    <option value="Учащийся/учащаяся" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Учащийся/учащаяся' ? 'selected' : '' ?>>Учащийся</option>
                    <option value="Другое" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Другое' ? 'selected' : '' ?>>Другое</option>
                </select>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Email адрес" 
                       value="<?= isset($oldData['email']) ? htmlspecialchars($oldData['email']) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="newPassword" placeholder="Пароль" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="confirmPassword" placeholder="Подтвердите пароль" required>
            </div>
            
            <input type="hidden" name="timezone" id="timezone" value="">
            
            <button type="submit">Зарегистрироваться</button>
        </form>
        
        <div class="link">
            <p>Уже есть аккаунт? <a href="/login">Войдите здесь</a></p>
        </div>
        
        <div class="link">
            <p><a href="/registration">← Обычная страница регистрации</a></p>
        </div>
    </div>
    
    <script>
        // Timezone detection
        function detectTimezone() {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            document.getElementById('timezone').value = timezone;
        }
        window.addEventListener('load', detectTimezone);
    </script>
</body>
</html>