<?php
// Ultimate Unified Template Engine - THE ONLY TEMPLATE ENGINE
// Load environment configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';

// Only require database connection if not already loaded
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

function renderTemplate($pageTitle, $mainContent, $additionalData = [], $metaD = null, $metaK = null) {
    global $connection;
    
    // Support for authorization layout (from template-engine-authorization.php)
    if (isset($additionalData['layoutType']) && $additionalData['layoutType'] === 'auth') {
        return renderAuthLayout($pageTitle, $mainContent, $additionalData);
    }
    
    // Handle backward compatibility for meta tags passed as separate parameters
    if ($metaD !== null) {
        $additionalData['metaD'] = $metaD;
    }
    if ($metaK !== null) {
        $additionalData['metaK'] = $metaK;
    }
    
    // Enhanced configuration options with defaults
    $config = array_merge([
        'layoutType' => 'default', // 'default', 'auth', 'dashboard', 'minimal', 'no-bootstrap'
        'cssFramework' => 'custom', // 'bootstrap', 'custom' - default to custom
        'headerType' => 'default', // 'default', 'modern', 'no-bootstrap'
        'footerType' => 'default', // 'default', 'modern'
        'noHeader' => false,
        'noFooter' => false,
        'noIndex' => false,
        'fullHeight' => false,
        'darkMode' => true,
        'customCSS' => '',
        'customJS' => '',
        'breadcrumbs' => [],
        'canonicalUrl' => '',
        'metaD' => 'Образовательный портал 11-классники - все для успешной сдачи ЕГЭ, ОГЭ и поступления в вуз.',
        'metaK' => '11 классников, образование, школа, вуз, егэ, огэ, тесты, новости образования'
    ], $additionalData);
    
    // Extract config for easier access
    extract($config);
    
    // Set darkModeEnabled for backward compatibility
    $darkModeEnabled = $darkMode;
    
    // Auto-detect framework based on layout type for backward compatibility
    if ($layoutType === 'no-bootstrap') {
        $cssFramework = 'custom';
        $headerType = 'no-bootstrap';
    }
    
    // Use environment-based debug mode if available
    if (Environment::isDebug()) {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    }
    
    // Extract additional data BEFORE including content
    if (!empty($additionalData)) {
        extract($additionalData);
    }
    
    // Start output buffering to capture the main content
    ob_start();
    include $_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent;
    $content = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="light" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
    
    <?php if ($noIndex): ?>
        <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>
    
    <?php if (isset($metaD) && !empty($metaD)): ?>
        <meta name="description" content="<?php echo htmlspecialchars($metaD); ?>">
    <?php endif; ?>
    
    <?php if (isset($metaK) && !empty($metaK)): ?>
        <meta name="keywords" content="<?php echo htmlspecialchars($metaK); ?>">
    <?php endif; ?>
    
    <!-- CSS Framework -->
    <?php if ($cssFramework === 'bootstrap'): ?>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>
    
    <!-- Unified Styles for improved design and dark mode -->
    <link href="/css/unified-styles.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- TinyMCE for dashboard -->
    <?php if ($layoutType === 'dashboard'): ?>
        <script src="https://cdn.tiny.cloud/1/your-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <?php endif; ?>
    
    <!-- Universal Styles -->
    <style>
        /* CSS Variables for consistent theming */
        :root {
            --primary-color: #28a745;
            --text-primary: #333;
            --text-secondary: #666;
            --background: #ffffff;
            --surface: #ffffff;
            --surface-variant: #f8f9fa;
            --border-color: #e2e8f0;
        }
        
        [data-theme="dark"] {
            --primary-color: #68d391;
            --text-primary: #f7fafc;
            --text-secondary: #cbd5e0;
            --background: #1a202c;
            --surface: #1e293b;
            --surface-variant: #2d3748;
            --border-color: #4a5568;
        }
        
        /* CSS Reset and Base Styles */
        <?php if ($cssFramework === 'custom'): ?>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Ensure all containers respect theme variables */
        .container, .row, .col-12, .col-6, .col-4, .col-3 {
            background: inherit;
        }
        <?php endif; ?>
        
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            <?php if ($cssFramework === 'bootstrap'): ?>
            background-color: var(--background, #ffffff);
            <?php else: ?>
            background: var(--background, #ffffff);
            <?php endif; ?>
            line-height: 1.6;
            color: var(--text-primary, #333);
            <?php if ($fullHeight || $layoutType === 'auth'): ?>
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            <?php else: ?>
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            <?php endif; ?>
        }
        
        
        footer, .modern-footer {
            margin-top: auto;
        }
        
        <?php if ($cssFramework === 'custom'): ?>
        /* Custom CSS Framework - Bootstrap Alternative */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            background: var(--background, transparent);
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }
        
        .col-12 { width: 100%; padding: 0 15px; }
        .col-6 { width: 50%; padding: 0 15px; }
        .col-4 { width: 33.333%; padding: 0 15px; }
        .col-3 { width: 25%; padding: 0 15px; }
        
        /* Utility Classes */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .d-flex { display: flex; }
        .d-none { display: none; }
        .mb-3 { margin-bottom: 1rem; }
        .mt-3 { margin-top: 1rem; }
        .p-3 { padding: 1rem; }
        
        /* Card Component */
        .card {
            background: var(--surface, white);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-primary, #333);
        }
        
        /* Button Styles */
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        /* Form Controls */
        .form-control, .form-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-color, #ddd);
            border-radius: 4px;
            font-size: 14px;
            background: var(--surface, white);
            color: var(--text-primary, #333);
        }
        
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color, #007bff);
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        
        /* Alert Component */
        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        @media (max-width: 768px) {
            .col-md-6, .col-lg-3, .col-lg-4, .col-lg-6, .col-lg-8, .col-lg-10 {
                width: 100%;
                margin-bottom: 20px;
            }
        }
        <?php endif; ?>
        
        <?php if ($darkModeEnabled): ?>
        /* Dark Mode Support */
        [data-theme="dark"] body {
            background: var(--background, #1a202c);
            color: var(--text-primary, #e4e6eb);
        }
        
        [data-theme="dark"] .card {
            background: #2d3748;
            color: #e4e6eb;
        }
        
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select {
            background: #2d3748;
            border-color: #4a5568;
            color: #e4e6eb;
        }
        
        [data-theme="dark"] .alert-info {
            background: #2c5282;
            color: #bee3f8;
            border-color: #3182ce;
        }
        
        /* Theme Toggle Button */
        .theme-toggle {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }
        
        .theme-toggle:hover {
            background-color: rgba(0,0,0,0.1);
        }
        
        [data-theme="dark"] .theme-toggle:hover {
            background-color: rgba(255,255,255,0.1);
        }
        <?php endif; ?>
        
        /* Mobile Optimizations */
        @media (max-width: 768px) {
            body {
                background: var(--background, #ffffff);
            }
            
            .card {
                box-shadow: none;
                border: 1px solid var(--border-color, #e9ecef);
            }
        }
    </style>
    
    <!-- Additional styles from content -->
    <?php if (isset($additionalStyles)): ?>
        <?php echo $additionalStyles; ?>
    <?php endif; ?>
    
    <!-- Immediate theme application to prevent FOUC (Flash of Unstyled Content) -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('preferred-theme') || 'light';
                document.documentElement.setAttribute('data-bs-theme', savedTheme);
                document.documentElement.setAttribute('data-theme', savedTheme);
            } catch(e) {
                // Fallback if localStorage is not available
                document.documentElement.setAttribute('data-bs-theme', 'light');
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    
    <!-- Define toggleTheme function early to ensure it's available -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme') || html.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-bs-theme', newTheme);
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('preferred-theme', newTheme);
            
            // Update theme icons
            const themeIcon = document.getElementById('theme-icon');
            const themeIconUser = document.getElementById('theme-icon-user');
            const iconClass = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            
            if (themeIcon) {
                themeIcon.className = iconClass;
            }
            if (themeIconUser) {
                themeIconUser.className = iconClass;
            }
        }
        
        // Make it globally available immediately
        window.toggleTheme = toggleTheme;
    </script>
</head>
<body>
    <!-- YouTube-style Loading Placeholders -->
    <?php 
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/loading-spinner.php';
    renderLoadingSpinner();
    ?>
    
    <!-- Header -->
    <?php if (!$noHeader): ?>
        <?php 
        // THE ONLY UNIFIED HEADER - Modern, Clean, Responsive
        include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php';
        ?>
    <?php endif; ?>
    
    <!-- Main Content -->
    <?php echo $content; ?>
    
    <!-- Footer -->
    <?php if (!$noFooter): ?>
        <?php 
        // USE ONLY ONE UNIFIED FOOTER
        include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php';
        ?>
    <?php endif; ?>
    
    <!-- JavaScript -->
    <?php if ($cssFramework === 'bootstrap'): ?>
        <!-- Bootstrap JS Bundle -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php endif; ?>
    
    <?php if ($darkModeEnabled): ?>
    <!-- Dark Mode Toggle Script -->
    <script>
        // Dark mode functionality
        if (typeof window.toggleTheme === 'undefined') {
            window.toggleTheme = function() {
                const html = document.documentElement;
                const currentTheme = html.getAttribute('data-bs-theme') || html.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                html.setAttribute('data-bs-theme', newTheme);
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('preferred-theme', newTheme);
                
                // Update theme icons (handle multiple icons for different user states)
                const themeIcon = document.getElementById('theme-icon');
                const themeIconUser = document.getElementById('theme-icon-user');
                const iconClass = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
                
                if (themeIcon) {
                    themeIcon.className = iconClass;
                }
                if (themeIconUser) {
                    themeIconUser.className = iconClass;
                }
            };
        }
        
        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('preferred-theme') || 'light';
            const html = document.documentElement;
            
            html.setAttribute('data-bs-theme', savedTheme);
            html.setAttribute('data-theme', savedTheme);
            
            const themeIcon = document.getElementById('theme-icon');
            const themeIconUser = document.getElementById('theme-icon-user');
            const iconClass = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            
            if (themeIcon) {
                themeIcon.className = iconClass;
            }
            if (themeIconUser) {
                themeIconUser.className = iconClass;
            }
        });
    </script>
    <?php endif; ?>
    
    <!-- Header Dropdown Fix -->
    <script>
        // Fix for Bootstrap dropdown issues
        document.addEventListener('DOMContentLoaded', function() {
            // Handle dropdown toggles
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            dropdownToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    if (<?php echo $cssFramework === 'custom' ? 'true' : 'false'; ?>) {
                        e.preventDefault();
                        const dropdown = toggle.closest('.dropdown');
                        dropdown.classList.toggle('show');
                        const menu = dropdown.querySelector('.dropdown-menu');
                        if (menu) {
                            menu.classList.toggle('show');
                        }
                    }
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown.show').forEach(function(dropdown) {
                        dropdown.classList.remove('show');
                        const menu = dropdown.querySelector('.dropdown-menu');
                        if (menu) {
                            menu.classList.remove('show');
                        }
                    });
                }
            });
        });
    </script>
    
    <!-- Additional scripts from content -->
    <?php if (isset($additionalScripts)): ?>
        <?php echo $additionalScripts; ?>
    <?php endif; ?>
</body>
</html>
<?php
}

/**
 * Authorization Layout (from template-engine-authorization.php)
 * For login, registration, and other auth pages
 */
function renderAuthLayout($pageTitle, $mainContent, $config = []) {
    // Default auth configuration
    $authConfig = array_merge([
        'noIndex' => true,
        'customCSS' => '/css/authorization.css'
    ], $config);
    
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex, nofollow">
        <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
        
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        
        <!-- Custom CSS -->
        <link rel="stylesheet" href="/css/unified-styles.css">
        <link rel="stylesheet" href="/css/site-logo.css">
        <?php if (!empty($authConfig['customCSS'])): ?>
            <link rel="stylesheet" href="<?php echo $authConfig['customCSS']; ?>">
        <?php endif; ?>
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        <!-- Favicon -->
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
    </head>
    <body class="full-height-flex">
        <main class="container">
            <div class="d-flex justify-content-center align-items-center min-vh-100">
                <?php include $_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent; ?>
            </div>
        </main>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <?php if (!empty($authConfig['customJS'])): ?>
            <script src="<?php echo $authConfig['customJS']; ?>"></script>
        <?php endif; ?>
    </body>
    </html>
    <?php
}
?>