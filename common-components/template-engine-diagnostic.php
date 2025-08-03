<?php
// THE ONE AND ONLY TEMPLATE ENGINE - Diagnostic Version
function renderTemplate($pageTitle, $contentFile, $templateConfig = [], $metaD = null, $metaK = null) {
    $additionalData = is_array($templateConfig) ? $templateConfig : [];
    
    // Handle backward compatibility for meta tags passed as separate parameters
    if ($metaD !== null) {
        $additionalData['metaD'] = $metaD;
    }
    if ($metaK !== null) {
        $additionalData['metaK'] = $metaK;
    }
    
    // Configuration options with defaults
    $config = array_merge([
        'layoutType' => 'default',
        'cssFramework' => 'custom',
        'headerType' => 'modern',
        'footerType' => 'modern',
        'darkMode' => true,
        'noHeader' => false,
        'noFooter' => false,
        'customCSS' => '',
        'customJS' => '',
        'breadcrumbs' => [],
        'metaD' => 'Образовательный портал 11-классники - все для успешной сдачи ЕГЭ, ОГЭ и поступления в вуз.',
        'metaK' => '11 классников, образование, школа, вуз, егэ, огэ, тесты, новости образования',
        'canonicalUrl' => ''
    ], $additionalData);
    
    // Extract variables for use in content files
    extract($additionalData);
    
    // Get current URL for canonical
    $currentUrl = $config['canonicalUrl'] ?: 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    ?>
    <!DOCTYPE html>
    <html lang="ru" data-theme="<?= $config['darkMode'] ? 'light' : 'light' ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($pageTitle) ?></title>
        <meta name="description" content="<?= htmlspecialchars($config['metaD']) ?>">
        <meta name="keywords" content="<?= htmlspecialchars($config['metaK']) ?>">
        <link rel="canonical" href="<?= htmlspecialchars($currentUrl) ?>">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Main CSS Framework -->
        <?php if ($config['cssFramework'] === 'custom'): ?>
            <link rel="stylesheet" href="/css/unified-styles.css">
        <?php endif; ?>
        
        <!-- Custom CSS -->
        <?php if (!empty($config['customCSS'])): ?>
            <style><?= $config['customCSS'] ?></style>
        <?php endif; ?>
        
        <!-- Dark Mode Variables and Theme Toggle Script -->
        <style>
            :root {
                --primary-color: #28a745;
                --secondary-color: #6c757d;
                --success-color: #28a745;
                --danger-color: #dc3545;
                --warning-color: #ffc107;
                --info-color: #17a2b8;
                --light-color: #f8f9fa;
                --dark-color: #343a40;
                --surface: #ffffff;
                --surface-variant: #f8f9fa;
                --text-primary: #212529;
                --text-secondary: #6c757d;
                --border-color: #dee2e6;
                --shadow-sm: 0 2px 10px rgba(0,0,0,0.08);
                --shadow-lg: 0 5px 20px rgba(0,0,0,0.1);
            }
            
            [data-theme="dark"] {
                --surface: #1e293b;
                --surface-variant: #334155;
                --text-primary: #e4e6eb;
                --text-secondary: #b3b8bd;
                --border-color: #4a5568;
                --shadow-sm: 0 2px 10px rgba(0,0,0,0.2);
                --shadow-lg: 0 5px 20px rgba(0,0,0,0.3);
            }
            
            body {
                background-color: var(--surface);
                color: var(--text-primary);
                transition: background-color 0.3s ease, color 0.3s ease;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                margin: 0;
                padding: 0;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            }
            
            .main-content {
                flex: 1;
                width: 100%;
            }
        </style>
        
        <script>
            // Theme toggle functionality - must be in head to prevent flash
            function toggleTheme() {
                const html = document.documentElement;
                const currentTheme = html.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('preferred-theme', newTheme);
                
                // Update theme toggle icons
                const iconClass = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                const themeIcon = document.getElementById('theme-icon');
                const themeIconUser = document.getElementById('theme-icon-user');
                
                if (themeIcon) themeIcon.className = iconClass;
                if (themeIconUser) themeIconUser.className = iconClass;
            }
            
            // Apply saved theme immediately
            (function() {
                const savedTheme = localStorage.getItem('preferred-theme');
                if (savedTheme) {
                    document.documentElement.setAttribute('data-theme', savedTheme);
                }
            })();
        </script>
        
        <!-- Custom JavaScript -->
        <?php if (!empty($config['customJS'])): ?>
            <script><?= $config['customJS'] ?></script>
        <?php endif; ?>
    </head>
    <body>
        <?php if (!$config['noHeader']): ?>
            <?php 
            // USE DIAGNOSTIC HEADER FOR DEBUGGING
            include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header-diagnostic.php';
            ?>
        <?php endif; ?>
        
        <main class="main-content">
            <?php 
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $contentFile)) {
                include $_SERVER['DOCUMENT_ROOT'] . '/' . $contentFile;
            } else {
                echo '<div style="padding: 50px; text-align: center;">';
                echo '<h1>Страница не найдена</h1>';
                echo '<p>Контент файл не найден: ' . htmlspecialchars($contentFile) . '</p>';
                echo '</div>';
            }
            ?>
        </main>
        
        <?php if (!$config['noFooter']): ?>
            <?php 
            // USE ONLY ONE UNIFIED FOOTER
            include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php';
            ?>
        <?php endif; ?>
    </body>
    </html>
    <?php
}
?>