<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check if user is admin - ADMINS CANNOT DELETE THEIR ACCOUNTS
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$username = $_SESSION['username'] ?? $_SESSION['email'] ?? 'Пользователь';

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить аккаунт - 11классники</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #28a745;
            --primary-dark: #218838;
            --danger: #dc3545;
            --danger-dark: #c82333;
            --warning: #ffc107;
            --light: #f8f9fa;
            --dark: #343a40;
            --white: #ffffff;
            --border: #dee2e6;
            --shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: var(--dark);
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, var(--danger), var(--danger-dark));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .content {
            padding: 30px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #666;
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left-color: var(--danger);
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left-color: var(--warning);
        }

        .alert h5 {
            margin-bottom: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert ul {
            margin: 10px 0 0 20px;
        }

        .card {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .card.border-danger {
            border-color: var(--danger);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--danger);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }

        .form-check {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
        }

        .form-check-input {
            margin-top: 3px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .form-check-label {
            cursor: pointer;
            line-height: 1.5;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: var(--danger-dark);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-outline-primary {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        .admin-protection {
            text-align: center;
            padding: 40px 20px;
        }

        .admin-protection .icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .admin-protection h3 {
            color: var(--warning);
            margin-bottom: 15px;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        @media (max-width: 768px) {
            .container {
                margin: 0;
                border-radius: 0;
                min-height: 100vh;
            }
            
            .content {
                padding: 20px;
            }
            
            .buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Удаление аккаунта</h1>
            <p>Осторожно! Необратимая операция</p>
        </div>
        
        <div class="content">
            <div class="breadcrumb">
                <a href="/">Главная</a> → 
                <a href="/account">Мой аккаунт</a> → 
                <span>Удалить аккаунт</span>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <h5>❌ Ошибка</h5>
                    <p><?= htmlspecialchars($_SESSION['error']) ?></p>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
                <!-- Admin Protection -->
                <div class="admin-protection">
                    <div class="icon">🛡️</div>
                    <h3>Защита администратора</h3>
                    <p>Аккаунты администраторов не могут быть удалены в целях безопасности системы.</p>
                    <p><strong>Ваш статус:</strong> Администратор (<?= htmlspecialchars($username) ?>)</p>
                    
                    <div class="alert alert-warning">
                        <h5>🔒 Безопасность системы</h5>
                        <p>Удаление административных аккаунтов заблокировано для предотвращения случайной потери доступа к системе управления.</p>
                    </div>
                    
                    <div class="buttons">
                        <a href="/account" class="btn btn-outline-primary">← Вернуться в аккаунт</a>
                        <a href="/dashboard" class="btn btn-secondary">Панель управления</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Regular User Delete Form -->
                <div class="alert alert-danger">
                    <h5>⚠️ Внимание!</h5>
                    <p>Удаление аккаунта является необратимой операцией. Будут безвозвратно удалены:</p>
                    <ul>
                        <li>Все ваши личные данные и настройки профиля</li>
                        <li>История активности и входов в систему</li>
                        <li>Все ваши комментарии и отзывы</li>
                        <li>Опубликованные материалы (для авторов)</li>
                        <li>Все связанные файлы и данные</li>
                    </ul>
                </div>

                <div class="card border-danger">
                    <h5 class="card-title">🔐 Подтверждение удаления</h5>
                    <p>Если вы уверены, что хотите удалить свой аккаунт, введите ваш пароль для подтверждения:</p>
                    
                    <form action="/pages/account/delete-account/delete-account-process-simple.php" method="post" onsubmit="return confirmDelete();">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Ваш пароль</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Введите текущий пароль">
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="confirm_delete" name="confirm_delete" required>
                            <label class="form-check-label" for="confirm_delete">
                                Я понимаю, что удаление аккаунта необратимо и все мои данные будут безвозвратно потеряны
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="confirm_final" name="confirm_final" required>
                            <label class="form-check-label" for="confirm_final">
                                Я подтверждаю, что хочу полностью удалить свой аккаунт
                            </label>
                        </div>
                        
                        <div class="buttons">
                            <a href="/account" class="btn btn-secondary">← Отмена</a>
                            <button type="submit" class="btn btn-danger" id="deleteBtn">
                                🗑️ Удалить аккаунт навсегда
                            </button>
                        </div>
                    </form>
                </div>

                <div class="alert alert-warning">
                    <h5>💡 Альтернативы удалению</h5>
                    <p>Возможно, вместо удаления аккаунта вы хотите:</p>
                    <ul>
                        <li><a href="/account/personal-data-change">Изменить личные данные</a></li>
                        <li><a href="/account/password-change">Сменить пароль</a></li>
                        <li>Временно не использовать аккаунт (просто не входите в систему)</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function confirmDelete() {
        const password = document.getElementById('password').value;
        const confirm1 = document.getElementById('confirm_delete').checked;
        const confirm2 = document.getElementById('confirm_final').checked;
        
        if (!password || !confirm1 || !confirm2) {
            alert('Пожалуйста, заполните все поля и поставьте обе галочки для подтверждения.');
            return false;
        }
        
        const finalConfirm = confirm(
            'ПОСЛЕДНЕЕ ПРЕДУПРЕЖДЕНИЕ!\n\n' +
            'Вы действительно хотите НАВСЕГДА удалить свой аккаунт?\n\n' +
            '• Все ваши данные будут БЕЗВОЗВРАТНО удалены\n' +
            '• Восстановление будет НЕВОЗМОЖНО\n' +
            '• Это действие НЕОБРАТИМО\n\n' +
            'Нажмите "ОК" только если вы полностью уверены.'
        );
        
        if (finalConfirm) {
            document.getElementById('deleteBtn').innerHTML = '⏳ Удаляем аккаунт...';
            document.getElementById('deleteBtn').disabled = true;
        }
        
        return finalConfirm;
    }
    </script>
</body>
</html>