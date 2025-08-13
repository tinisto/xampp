<?php
// Simple homepage with categories dropdown
// Load database connection
require_once __DIR__ . '/database/db_connections.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>11klassniki - Образовательный портал</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --background: #ffffff;
            --surface: #f8f9fa;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        [data-theme="dark"] {
            --background: #1a202c;
            --surface: #2d3748;
            --text-primary: #f7fafc;
            --text-secondary: #a0aec0;
            --border-color: #4a5568;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.2);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.3);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.4);
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Header */
        .header {
            background: var(--surface);
            border-bottom: 1px solid var(--border-color);
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        
        .nav-link {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--primary-color);
        }
        
        /* Dropdown */
        .dropdown {
            position: relative;
        }
        
        .dropdown-toggle::after {
            content: ' ▼';
            font-size: 12px;
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: var(--surface);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-item {
            display: block;
            padding: 12px 20px;
            color: var(--text-primary);
            text-decoration: none;
            transition: background 0.2s ease;
        }
        
        .dropdown-item:hover {
            background: var(--background);
            color: var(--primary-color);
        }
        
        .dropdown-item:first-child {
            border-radius: 8px 8px 0 0;
        }
        
        .dropdown-item:last-child {
            border-radius: 0 0 8px 8px;
        }
        
        /* Theme Toggle */
        .theme-toggle {
            background: var(--surface);
            border: 2px solid var(--border-color);
            color: var(--text-primary);
            width: 40px;
            height: 40px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
        }
        
        .btn {
            padding: 14px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-primary {
            background: white;
            color: #667eea;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-secondary:hover {
            background: white;
            color: #667eea;
        }
        
        /* Features Grid */
        .features {
            padding: 80px 0;
            background: var(--background);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .section-title {
            font-size: 36px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 50px;
            color: var(--text-primary);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .feature-card {
            background: var(--surface);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
            color: white;
        }
        
        .feature-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-primary);
        }
        
        .feature-description {
            color: var(--text-secondary);
            line-height: 1.6;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-links {
                gap: 15px;
            }
            
            .hero h1 {
                font-size: 36px;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body data-theme="light">
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="/" class="logo">11klassniki</a>
            <nav class="nav-links">
                <a href="/" class="nav-link">Главная</a>
                
                <!-- Categories Dropdown -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Категории</a>
                    <div class="dropdown-menu">
                        <?php
                        try {
                            if ($connection && !$connection->connect_error) {
                                $query = "SELECT url_category, title_category FROM categories ORDER BY title_category";
                                $result = mysqli_query($connection, $query);
                                
                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($category = mysqli_fetch_assoc($result)) {
                                        echo '<a href="/category/' . htmlspecialchars($category['url_category']) . '" class="dropdown-item">' . 
                                             htmlspecialchars($category['title_category']) . '</a>';
                                    }
                                } else {
                                    echo '<a href="#" class="dropdown-item">Категории не найдены</a>';
                                }
                            }
                        } catch (Exception $e) {
                            echo '<a href="#" class="dropdown-item">Ошибка загрузки</a>';
                        }
                        ?>
                    </div>
                </div>
                
                <a href="/vpo-all-regions" class="nav-link">ВУЗы</a>
                <a href="/spo-all-regions" class="nav-link">ССУЗы</a>
                <a href="/schools-all-regions" class="nav-link">Школы</a>
                <a href="/news" class="nav-link">Новости</a>
                <a href="/tests" class="nav-link">Тесты</a>
                
                <button class="theme-toggle" onclick="toggleTheme()" title="Изменить тему">
                    <i class="fas fa-sun" id="themeIcon"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Образовательный портал 11klassniki</h1>
            <p>Все для успешной сдачи ЕГЭ, ОГЭ и поступления в вуз</p>
            <div class="hero-buttons">
                <a href="/tests" class="btn btn-primary">Пройти тесты</a>
                <a href="/news" class="btn btn-secondary">Читать новости</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Наши возможности</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="feature-title">Образовательные учреждения</h3>
                    <p class="feature-description">Полная база данных школ, вузов и ссузов по всей России</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3 class="feature-title">Онлайн тесты</h3>
                    <p class="feature-description">Проверьте свои знания с помощью интерактивных тестов</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h3 class="feature-title">Новости образования</h3>
                    <p class="feature-description">Актуальные новости и изменения в сфере образования</p>
                </div>
            </div>
        </div>
    </section>
    
    <script>
        // Theme toggle functionality
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('themeIcon');
            const currentTheme = body.getAttribute('data-theme');
            
            if (currentTheme === 'dark') {
                body.setAttribute('data-theme', 'light');
                themeIcon.className = 'fas fa-sun';
            } else {
                body.setAttribute('data-theme', 'dark');
                themeIcon.className = 'fas fa-moon';
            }
            
            localStorage.setItem('theme', body.getAttribute('data-theme'));
        }
        
        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const themeIcon = document.getElementById('themeIcon');
            
            document.body.setAttribute('data-theme', savedTheme);
            
            if (savedTheme === 'dark') {
                themeIcon.className = 'fas fa-moon';
            }
        });
    </script>
</body>
</html>