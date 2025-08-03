<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/SessionManager.php';
SessionManager::start();

// Check if user is admin
if (!SessionManager::isLoggedIn() || SessionManager::get('role') !== 'admin') {
    header("Location: /login");
    exit();
}

$pageTitle = 'Loading Placeholders - Admin Dashboard';
$metaD = 'Управление загрузочными заглушками';
$metaK = 'admin, dashboard, placeholders';

// Include placeholder component
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/loading-placeholders-v2.php';
?>

<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="/css/unified-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .admin-header {
            background: var(--surface, #ffffff);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .placeholder-section {
            background: var(--surface, #ffffff);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-primary, #333);
        }
        .code-example {
            background: var(--background, #f5f5f5);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 6px;
            padding: 15px;
            margin-top: 15px;
            font-family: monospace;
            font-size: 14px;
            overflow-x: auto;
        }
        .implementation-guide {
            background: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        [data-theme="dark"] .implementation-guide {
            background: #1e3a5f;
            border-color: #42a5f5;
        }
        .preview-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .control-btn {
            padding: 8px 16px;
            background: var(--primary-color, #28a745);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .control-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <main style="flex: 1;">
        <div class="admin-container">
            <!-- Admin Header -->
            <div class="admin-header">
                <h1 style="margin: 0 0 10px 0;">Loading Placeholders System</h1>
                <p style="color: var(--text-secondary, #666); margin: 0;">
                    Контекстно-зависимые загрузочные заглушки для улучшения UX
                </p>
                <div style="margin-top: 20px;">
                    <a href="/dashboard" class="control-btn" style="text-decoration: none; display: inline-block;">
                        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                        Вернуться в Dashboard
                    </a>
                </div>
            </div>

            <!-- Preview Controls -->
            <div class="placeholder-section">
                <h2 class="section-title">Демо управление</h2>
                <div class="preview-controls">
                    <button class="control-btn" onclick="toggleTheme()">
                        <i class="fas fa-moon" style="margin-right: 8px;"></i>
                        Переключить тему
                    </button>
                    <button class="control-btn" onclick="toggleAnimation()">
                        <i class="fas fa-pause" style="margin-right: 8px;"></i>
                        Вкл/Выкл анимацию
                    </button>
                    <button class="control-btn" onclick="simulateLoading()">
                        <i class="fas fa-sync" style="margin-right: 8px;"></i>
                        Симулировать загрузку
                    </button>
                </div>
            </div>

            <!-- News Cards -->
            <div class="placeholder-section">
                <h2 class="section-title">News Cards (Карточки новостей)</h2>
                <div id="news-demo">
                    <?php renderPlaceholderGrid('news-card', 4, 4); ?>
                </div>
                <div class="code-example">
&lt;?php renderPlaceholderGrid('news-card', 4, 4); ?&gt;
                </div>
            </div>

            <!-- Post Cards -->
            <div class="placeholder-section">
                <h2 class="section-title">Post Cards (Карточки статей)</h2>
                <?php renderPlaceholderGrid('post-card', 3, 3, ['showImage' => true, 'showBadge' => true]); ?>
                <div class="code-example">
&lt;?php renderPlaceholderGrid('post-card', 3, 3, ['showImage' => true, 'showBadge' => true]); ?&gt;
                </div>
            </div>

            <!-- School Cards -->
            <div class="placeholder-section">
                <h2 class="section-title">School Cards (Карточки учебных заведений)</h2>
                <?php renderPlaceholderGrid('school-card', 2, 2); ?>
                <div class="code-example">
&lt;?php renderPlaceholderGrid('school-card', 2, 2); ?&gt;
                </div>
            </div>

            <!-- Test Cards -->
            <div class="placeholder-section">
                <h2 class="section-title">Test Cards (Карточки тестов)</h2>
                <?php renderPlaceholderGrid('test-card', 6, 3); ?>
                <div class="code-example">
&lt;?php renderPlaceholderGrid('test-card', 6, 3); ?&gt;
                </div>
            </div>

            <!-- Category Header -->
            <div class="placeholder-section">
                <h2 class="section-title">Category Header (Заголовок категории)</h2>
                <?php renderContextAwarePlaceholder('category-header'); ?>
                <div class="code-example">
&lt;?php renderContextAwarePlaceholder('category-header'); ?&gt;
                </div>
            </div>

            <!-- Article Content -->
            <div class="placeholder-section">
                <h2 class="section-title">Article Content (Содержимое статьи)</h2>
                <?php renderContextAwarePlaceholder('article-content'); ?>
                <div class="code-example">
&lt;?php renderContextAwarePlaceholder('article-content'); ?&gt;
                </div>
            </div>

            <!-- Implementation Guide -->
            <div class="implementation-guide">
                <h3 style="margin-top: 0;">📋 Руководство по внедрению</h3>
                
                <h4>1. Подключение компонента:</h4>
                <div class="code-example" style="background: white;">
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/loading-placeholders-v2.php';
                </div>
                
                <h4>2. Использование с AJAX загрузкой:</h4>
                <div class="code-example" style="background: white;">
&lt;div id="content-container" 
     data-lazy-load="/api/get-content.php"
     data-placeholder-type="news-card"
     data-placeholder-count="12"
     data-placeholder-columns="4"&gt;
    &lt;!-- Заглушки будут показаны автоматически --&gt;
&lt;/div&gt;

&lt;script src="/js/lazy-content-loader.js"&gt;&lt;/script&gt;
                </div>
                
                <h4>3. Доступные типы заглушек:</h4>
                <ul>
                    <li><code>news-card</code> - для новостей</li>
                    <li><code>post-card</code> - для статей с изображениями</li>
                    <li><code>school-card</code> - для учебных заведений</li>
                    <li><code>test-card</code> - для тестов</li>
                    <li><code>category-header</code> - для заголовков категорий</li>
                    <li><code>article-content</code> - для полного текста статьи</li>
                    <li><code>comment</code> - для комментариев</li>
                    <li><code>table-row</code> - для строк таблицы</li>
                </ul>
                
                <h4>4. Параметры:</h4>
                <ul>
                    <li><code>count</code> - количество заглушек</li>
                    <li><code>columns</code> - количество колонок в сетке</li>
                    <li><code>showImage</code> - показывать область изображения</li>
                    <li><code>showBadge</code> - показывать бейдж категории</li>
                    <li><code>showMeta</code> - показывать мета-информацию</li>
                    <li><code>animated</code> - включить анимацию (по умолчанию true)</li>
                </ul>
            </div>
        </div>
    </main>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
    
    <script src="/js/lazy-content-loader.js"></script>
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            html.setAttribute('data-theme', newTheme);
            html.setAttribute('data-bs-theme', newTheme);
        }
        
        function toggleAnimation() {
            const skeletons = document.querySelectorAll('.skeleton');
            skeletons.forEach(skeleton => {
                skeleton.classList.toggle('skeleton-animated');
            });
        }
        
        function simulateLoading() {
            const newsDemo = document.getElementById('news-demo');
            const originalContent = newsDemo.innerHTML;
            
            // Show loading message
            newsDemo.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 48px; color: var(--primary-color, #28a745);"></i><p style="margin-top: 20px;">Загрузка контента...</p></div>';
            
            // Restore placeholders after 2 seconds
            setTimeout(() => {
                newsDemo.innerHTML = originalContent;
            }, 2000);
        }
    </script>
</body>
</html>