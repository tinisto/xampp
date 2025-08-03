<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Get user data
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection->set_charset("utf8mb4");

$userId = $_SESSION['user_id'];
$occupation = $_SESSION["occupation"] ?? '';

// Fetch counts
$commentsCount = 0;
$newsCount = 0;

$stmt = $connection->prepare("SELECT COUNT(*) as count FROM comments WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$commentsCount = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

$stmt = $connection->prepare("SELECT COUNT(*) as count FROM news WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$newsCount = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - 11-классники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Light mode (default) */
        [data-bs-theme="light"] {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
            --card-bg: #ffffff;
            --hover-bg: #e9ecef;
            --danger-bg: #fff5f5;
            --danger-hover-bg: #ffebeb;
        }
        
        /* Dark mode */
        [data-bs-theme="dark"] {
            --bg-primary: #212529;
            --bg-secondary: #343a40;
            --text-primary: #f8f9fa;
            --text-secondary: #adb5bd;
            --border-color: #495057;
            --card-bg: #343a40;
            --hover-bg: #495057;
            --danger-bg: rgba(220, 53, 69, 0.1);
            --danger-hover-bg: rgba(220, 53, 69, 0.2);
        }
        
        body {
            background-color: var(--bg-secondary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .page-container {
            min-height: 100vh;
            padding: 40px 0;
        }
        
        @media (max-width: 768px) {
            .page-container {
                padding: 0;
            }
            .menu-item {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 0 1px 0;
                background: var(--card-bg);
            }
            .menu-item:hover {
                box-shadow: none !important;
                transform: none !important;
            }
        }
        .content-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 40px;
            position: relative;
        }
        
        @media (max-width: 768px) {
            .content-wrapper {
                border-radius: 0;
                box-shadow: none;
                margin: 0;
                padding: 20px;
            }
        }
        .theme-toggle-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--text-primary);
            z-index: 10;
        }
        
        @media (max-width: 768px) {
            .theme-toggle-btn {
                position: fixed;
                top: 10px;
                right: 10px;
                z-index: 1000;
            }
        }
        .theme-toggle-btn:hover {
            background: var(--hover-bg);
            transform: scale(1.1);
        }
        .page-title {
            font-size: 32px;
            font-weight: 600;
            color: #28a745;
            margin-bottom: 40px;
            text-align: center;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .menu-item {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 25px;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .menu-item:hover {
            background: var(--hover-bg);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-decoration: none;
            color: var(--text-primary);
        }
        .menu-item i {
            font-size: 24px;
            color: #28a745;
            width: 30px;
            text-align: center;
        }
        .menu-text {
            flex: 1;
        }
        .menu-title {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 4px;
        }
        .menu-description {
            font-size: 13px;
            color: var(--text-secondary);
            margin: 0;
        }
        .badge {
            background-color: #28a745;
            color: white;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 12px;
        }
        .logout-item {
            border-color: #dc3545;
        }
        .logout-item i {
            color: #dc3545;
        }
        .logout-item:hover {
            background: var(--danger-hover-bg);
        }
        .delete-item {
            border-color: #dc3545;
            background: var(--danger-bg);
        }
        .delete-item i {
            color: #dc3545;
        }
        .delete-item:hover {
            background: var(--danger-hover-bg);
        }
        
        /* Back link styling */
        .back-link {
            color: #28a745;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: #218838;
        }
    </style>
    <script>
        // Theme toggle function
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update icon
            const icon = document.getElementById('theme-icon');
            icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
        
        // Apply saved theme on load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            
            const icon = document.getElementById('theme-icon');
            icon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });
    </script>
</head>
<body>
    <div class="page-container">
        <div class="content-wrapper">
            <!-- Dark mode toggle button -->
            <button type="button" class="theme-toggle-btn" onclick="toggleTheme()" title="Переключить тему">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
            
            <!-- Home link separate at top -->
            <div style="margin-bottom: 30px;">
                <a href="/" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    На главную страницу
                </a>
            </div>
            
            <h1 class="page-title">Личный кабинет</h1>
            
            <div class="menu-grid">
                
                <a href="/account/personal-data-change" class="menu-item">
                    <i class="fas fa-user"></i>
                    <div class="menu-text">
                        <div class="menu-title">Личные данные</div>
                        <p class="menu-description">Изменить имя и контактную информацию</p>
                    </div>
                </a>
                
                <a href="/account/password-change" class="menu-item">
                    <i class="fas fa-lock"></i>
                    <div class="menu-text">
                        <div class="menu-title">Сменить пароль</div>
                        <p class="menu-description">Обновить пароль для входа</p>
                    </div>
                </a>
                
                <a href="/account/avatar" class="menu-item">
                    <i class="fas fa-image"></i>
                    <div class="menu-text">
                        <div class="menu-title">Аватар</div>
                        <p class="menu-description">Загрузить или изменить фото профиля</p>
                    </div>
                </a>
                
                <?php if ($occupation === "Представитель ВУЗа" || 
                         $occupation === "Представитель ССУЗа" || 
                         $occupation === "Представитель школы"): ?>
                <a href="/account/representative" class="menu-item">
                    <i class="fas fa-university"></i>
                    <div class="menu-text">
                        <div class="menu-title">Для представителя</div>
                        <p class="menu-description">Управление учебным заведением</p>
                    </div>
                </a>
                <?php endif; ?>
                
                <a href="/account/comments-user" class="menu-item">
                    <i class="fas fa-comments"></i>
                    <div class="menu-text">
                        <div class="menu-title">Мои комментарии</div>
                        <p class="menu-description">Просмотр всех комментариев</p>
                    </div>
                    <?php if ($commentsCount > 0): ?>
                    <span class="badge"><?= $commentsCount ?></span>
                    <?php endif; ?>
                </a>
                
                <a href="/account/news-user" class="menu-item">
                    <i class="fas fa-newspaper"></i>
                    <div class="menu-text">
                        <div class="menu-title">Мои новости</div>
                        <p class="menu-description">Управление публикациями</p>
                    </div>
                    <?php if ($newsCount > 0): ?>
                    <span class="badge"><?= $newsCount ?></span>
                    <?php endif; ?>
                </a>
                
                <a href="/logout" class="menu-item logout-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <div class="menu-text">
                        <div class="menu-title">Выйти</div>
                        <p class="menu-description">Завершить текущий сеанс</p>
                    </div>
                </a>
                
                <a href="/account/delete-account" class="menu-item delete-item">
                    <i class="fas fa-trash-alt"></i>
                    <div class="menu-text">
                        <div class="menu-title">Удалить аккаунт</div>
                        <p class="menu-description">Безвозвратное удаление</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>