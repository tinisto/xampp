<?php
/**
 * Modern Template Engine with CSS Variables
 * Implements YouTube-style theming system
 */

function renderTemplate($pageTitle, $mainContent, $templateConfig = []) {
    // Extract configuration with defaults
    $layoutType = $templateConfig['layoutType'] ?? 'default';
    $headerType = $templateConfig['headerType'] ?? 'modern';
    $footerType = $templateConfig['footerType'] ?? 'modern';
    $showBreadcrumb = $templateConfig['showBreadcrumb'] ?? true;
    $additionalCSS = $templateConfig['additionalCSS'] ?? [];
    $additionalJS = $templateConfig['additionalJS'] ?? [];
    $metaDescription = $templateConfig['metaD'] ?? '';
    $metaKeywords = $templateConfig['metaK'] ?? '';
    $bodyClass = $templateConfig['bodyClass'] ?? '';
    $darkMode = $templateConfig['darkMode'] ?? true;
    
    // Include SessionManager if it exists
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/includes/SessionManager.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/SessionManager.php';
    }
    
    // Get current theme from session/cookie
    $currentTheme = $_COOKIE['preferred-theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="ru" data-theme="<?= htmlspecialchars($currentTheme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= htmlspecialchars($pageTitle) ?> - 11klassniki.ru</title>
    
    <?php if ($metaDescription): ?>
    <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <?php endif; ?>
    
    <?php if ($metaKeywords): ?>
    <meta name="keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
    <?php endif; ?>
    
    <!-- Modern Theme Variables -->
    <link rel="stylesheet" href="/css/theme-variables.css">
    
    <!-- Core Styles -->
    <style>
        /* Immediate theme application */
        [data-theme="light"] {
            background-color: #ffffff;
            color: #212529;
        }
        
        [data-theme="dark"] {
            background-color: #0f0f0f;
            color: #f1f1f1;
        }
        
        /* Base Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            background-color: var(--color-surface-primary);
            color: var(--color-text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Main Content Area */
        .main-content {
            flex: 1;
            width: 100%;
        }
        
        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Links */
        a {
            color: var(--color-link);
            text-decoration: none;
            transition: color var(--transition-fast);
        }
        
        a:hover {
            color: var(--color-link-hover);
        }
        
        /* Headings */
        h1, h2, h3, h4, h5, h6 {
            color: var(--color-text-primary);
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 0.5em;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: var(--color-surface-secondary);
            color: var(--color-text-primary);
            border: 1px solid var(--color-border-primary);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition-fast);
        }
        
        .btn:hover {
            background-color: var(--color-bg-hover);
            border-color: var(--color-border-secondary);
            transform: translateY(-1px);
        }
        
        .btn-primary {
            background-color: var(--color-primary);
            color: var(--color-text-inverse);
            border-color: var(--color-primary);
        }
        
        .btn-primary:hover {
            background-color: var(--color-primary-hover);
            border-color: var(--color-primary-hover);
        }
        
        /* Cards */
        .card {
            background-color: var(--color-card-bg);
            border: 1px solid var(--color-border-primary);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all var(--transition-normal);
        }
        
        .card:hover {
            border-color: var(--color-border-secondary);
            box-shadow: 0 4px 12px var(--color-shadow-sm);
        }
        
        /* Forms */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            background-color: var(--color-surface-secondary);
            color: var(--color-text-primary);
            border: 1px solid var(--color-border-primary);
            border-radius: 8px;
            font-size: 16px;
            transition: all var(--transition-fast);
        }
        
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            background-color: var(--color-surface-primary);
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--color-surface-primary);
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--color-border-primary);
        }
        
        th {
            background-color: var(--color-surface-secondary);
            font-weight: 600;
            color: var(--color-text-primary);
        }
        
        tr:hover {
            background-color: var(--color-bg-hover);
        }
        
        /* Utility Classes */
        .mt-1 { margin-top: 8px; }
        .mt-2 { margin-top: 16px; }
        .mt-3 { margin-top: 24px; }
        .mt-4 { margin-top: 32px; }
        .mb-1 { margin-bottom: 8px; }
        .mb-2 { margin-bottom: 16px; }
        .mb-3 { margin-bottom: 24px; }
        .mb-4 { margin-bottom: 32px; }
        .text-center { text-align: center; }
        .text-muted { color: var(--color-text-secondary); }
        
        /* Loading state */
        .skeleton {
            background: linear-gradient(90deg, 
                var(--color-surface-secondary) 25%, 
                var(--color-surface-tertiary) 50%, 
                var(--color-surface-secondary) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Focus visible for accessibility */
        :focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }
        
        /* Selection */
        ::selection {
            background-color: var(--color-primary);
            color: var(--color-text-inverse);
        }
        
        /* Print styles */
        @media print {
            * {
                background: transparent !important;
                color: black !important;
            }
        }
    </style>
    
    <!-- Theme initialization (before any visual rendering) -->
    <script>
        // Immediately apply saved theme to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('preferred-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            // Also set cookie for PHP
            document.cookie = `preferred-theme=${savedTheme};path=/;max-age=31536000`;
        })();
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Additional CSS -->
    <?php foreach ($additionalCSS as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endforeach; ?>
</head>
<body class="<?= htmlspecialchars($bodyClass) ?>">
    
    <?php
    // Include header
    $headerFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php';
    if (file_exists($headerFile)) {
        include $headerFile;
    }
    ?>
    
    <main class="main-content">
        <?php
        // Include main content
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent)) {
            include $_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent;
        } else {
            echo '<div class="container mt-4"><div class="card"><h2>Страница не найдена</h2><p>Запрашиваемая страница не существует.</p></div></div>';
        }
        ?>
    </main>
    
    <?php
    // Include footer
    $footerFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer.php';
    if (file_exists($footerFile)) {
        include $footerFile;
    }
    ?>
    
    <!-- Theme Toggle Script -->
    <script>
        // Modern theme toggle with smooth transitions
        function toggleTheme() {
            // Disable transitions temporarily
            document.documentElement.classList.add('theme-transition-disabled');
            
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Update theme
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('preferred-theme', newTheme);
            document.cookie = `preferred-theme=${newTheme};path=/;max-age=31536000`;
            
            // Update all theme toggle buttons
            document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                }
            });
            
            // Re-enable transitions
            setTimeout(() => {
                document.documentElement.classList.remove('theme-transition-disabled');
            }, 50);
        }
        
        // Attach to all theme toggle buttons
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
                btn.addEventListener('click', toggleTheme);
            });
        });
        
        // Listen for system theme changes
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (!localStorage.getItem('preferred-theme')) {
                    const newTheme = e.matches ? 'dark' : 'light';
                    document.documentElement.setAttribute('data-theme', newTheme);
                }
            });
        }
    </script>
    
    <!-- Additional JS -->
    <?php foreach ($additionalJS as $js): ?>
    <script src="<?= htmlspecialchars($js) ?>"></script>
    <?php endforeach; ?>
</body>
</html>
<?php
}
?>