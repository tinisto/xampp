<?php
// Simple tests listing page without template dependencies
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Онлайн тесты - 11klassniki</title>
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
            background: rgba(var(--surface), 0.95);
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
        }
        
        .nav-link:hover {
            color: var(--primary-color);
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
        
        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .page-title {
            font-size: 36px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
            color: var(--text-primary);
        }
        
        .page-subtitle {
            font-size: 18px;
            text-align: center;
            color: var(--text-secondary);
            margin-bottom: 50px;
        }
        
        /* Category Section */
        .category-section {
            margin-bottom: 50px;
        }
        
        .category-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-primary);
            padding-left: 10px;
            border-left: 4px solid var(--primary-color);
        }
        
        /* Test Grid */
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .test-card {
            background: var(--surface);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
            overflow: hidden;
        }
        
        .test-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .test-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }
        
        .test-card:hover::before {
            transform: translateX(0);
        }
        
        .test-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            color: white;
            font-size: 28px;
            transition: all 0.3s ease;
        }
        
        .test-card:hover .test-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .test-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            color: var(--text-primary);
        }
        
        .test-description {
            font-size: 14px;
            color: var(--text-secondary);
            margin-top: 8px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-links {
                gap: 15px;
            }
            
            .page-title {
                font-size: 28px;
            }
            
            .test-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 16px;
            }
            
            .nav-link {
                font-size: 14px;
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
                <a href="/tests" class="nav-link">Тесты</a>
                <a href="/news" class="nav-link">Новости</a>
                <button class="theme-toggle" onclick="toggleTheme()" title="Изменить тему">
                    <i class="fas fa-sun" id="themeIcon"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <h1 class="page-title">Онлайн тесты</h1>
        <p class="page-subtitle">Проверьте свои знания и навыки с помощью наших интерактивных тестов</p>
        
        <!-- Academic Tests -->
        <div class="category-section">
            <h2 class="category-title">Академические предметы</h2>
            <div class="test-grid">
                <a href="/test-direct.php?test=math-test" class="test-card">
                    <div class="test-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="test-title">Математика</h3>
                    <p class="test-description">Проверьте знания по алгебре и геометрии</p>
                </a>
                
                <a href="/test-direct.php?test=russian-test" class="test-card">
                    <div class="test-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3 class="test-title">Русский язык</h3>
                    <p class="test-description">Грамматика, орфография и пунктуация</p>
                </a>
                
                <a href="/test-direct.php?test=physics-test" class="test-card">
                    <div class="test-icon">
                        <i class="fas fa-atom"></i>
                    </div>
                    <h3 class="test-title">Физика</h3>
                    <p class="test-description">Механика, электричество и оптика</p>
                </a>
                
                <a href="/test-direct.php?test=chemistry-test" class="test-card">
                    <div class="test-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h3 class="test-title">Химия</h3>
                    <p class="test-description">Органическая и неорганическая химия</p>
                </a>
                
                <a href="/test-direct.php?test=biology-test" class="test-card">
                    <div class="test-icon">
                        <i class="fas fa-dna"></i>
                    </div>
                    <h3 class="test-title">Биология</h3>
                    <p class="test-description">Анатомия, ботаника и зоология</p>
                </a>
                
                <a href="/test-direct.php?test=geography-test" class="test-card">
                    <div class="test-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3 class="test-title">География</h3>
                    <p class="test-description">Страны, столицы и природа</p>
                </a>
            </div>
        </div>
        
        <!-- Career and Personality Tests -->
        <div class="category-section">
            <h2 class="category-title">Профориентация и личность</h2>
            <div class="test-grid">
                <a href="/test-direct.php?test=career-test" class="test-card">
                    <div class="test-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3 class="test-title">Тест на профессию</h3>
                    <p class="test-description">Найдите подходящую карьеру</p>
                </a>
                
                <a href="/test-direct.php?test=personality-test" class="test-card">
                    <div class="test-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="test-title">Личностный тест</h3>
                    <p class="test-description">Узнайте свой тип личности</p>
                </a>
                
                <a href="/test-direct.php?test=iq-test" class="test-card">
                    <div class="test-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="test-title">IQ Тест</h3>
                    <p class="test-description">Проверьте уровень интеллекта</p>
                </a>
                
                <a href="/test-direct.php?test=aptitude-test" class="test-card">
                    <div class="test-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="test-title">Тест способностей</h3>
                    <p class="test-description">Оцените свои навыки</p>
                </a>
            </div>
        </div>
    </div>
    
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
            } else {
                themeIcon.className = 'fas fa-sun';
            }
        });
    </script>
</body>
</html>