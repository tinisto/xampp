<?php
// Include security configuration first
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/security/security_config.php';
// Include SEO configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/seo/seo_config.php';
// Include performance configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/performance/performance_config.php';
// Include dark mode configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ui/dark_mode_config.php';
// Include dark mode helpers
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/dark-mode-helpers.php';

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/session_util.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/auth.php";
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/redirectToErrorPage.php";

$baseUrl = '/';

// Single unified template function - handles all layouts
function renderTemplate(
    $pageTitle,
    $mainContent,
    $additionalData = [],
    $metaD = "",
    $metaK = "",
    $table = '',
    $countField = '',
    $linkPrefix = '',
    $options = []
) {
    global $connection, $baseUrl;
    
    // Default options - can be overridden
    $defaultOptions = [
        'header' => true,
        'footer' => true,
        'seo' => 'full',
        'analytics' => true,
        'darkMode' => true,
        'container' => 'container my-3',
        'bodyClass' => 'full-height-flex',
        'css' => ['styles.css', 'post-styles.css', 'buttons-styles.css', 'test.css', 'dark-mode-fix.css'],
        'robotsMeta' => 'index,follow'
    ];
    
    // Merge user options with defaults
    $layout = array_merge($defaultOptions, $options);
    
    // Set security headers
    setSecurityHeaders();
    
    // Prepare SEO options
    $seoOptions = [];
    if (isset($layout['robotsMeta'])) {
        $seoOptions['robots'] = $layout['robotsMeta'];
    }
    
    if ($metaD && $layout['seo'] === 'full') {
        $seoOptions['description'] = is_array($metaD) ? implode(", ", $metaD) : $metaD;
    }
    if ($metaK && $layout['seo'] === 'full') {
        $seoOptions['keywords'] = is_array($metaK) ? implode(", ", $metaK) : $metaK;
    }
    
    // Optimize page title for SEO
    $optimizedTitle = $layout['seo'] === 'full' ? optimizeTitle($pageTitle) : $pageTitle;
    $seoOptions['title'] = $optimizedTitle;
    
    // Generate meta tags
    $metaTags = generateMetaTags($seoOptions);
    
    // Generate structured data only for full SEO
    $structuredData = $layout['seo'] === 'full' ? generateStructuredData() : '';

    // Start HTML output
    echo <<<HTML
        <!DOCTYPE html>
        <html lang='ru'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            $metaTags
            
            <!-- Critical theme detection script -->
            <script>
                // Apply theme immediately to prevent any flash
                (function() {
                    try {
                        var savedTheme = localStorage.getItem('theme') || 'light';
                        console.log('Theme script running, savedTheme:', savedTheme);
                        document.documentElement.setAttribute('data-theme', savedTheme);
                        document.documentElement.className = savedTheme + '-theme';
                        
                        // Force body styling immediately
                        if (savedTheme === 'dark') {
                            document.documentElement.style.setProperty('--bg-primary', '#0f0f23');
                            document.documentElement.style.setProperty('--text-primary', '#fff');
                        } else {
                            document.documentElement.style.setProperty('--bg-primary', 'white');
                            document.documentElement.style.setProperty('--text-primary', '#212529');
                        }
                    } catch (e) {
                        console.error('Theme script error:', e);
                    }
                })();
            </script>
HTML;

    if ($layout['seo'] === 'full') {
        echo <<<HTML
            <!-- Adsense Meta tag -->
            <meta name="google-adsense-account" content="ca-pub-2363662533799826">
            <link rel='canonical' href='" . getCurrentUrl() . "'>
HTML;
    }

    // Include logo component for favicon
    require_once $_SERVER['DOCUMENT_ROOT'] . '/components/logo-component.php';
    renderFavicon();
    
    echo <<<HTML
            <!-- Bootstrap CSS FIRST -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
            <!-- Font Awesome -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <!-- Modern Design System -->
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
            
            <style>
                /* Light mode (default) */
                :root {
                    --bg-primary: #ffffff;
                    --bg-primary-rgb: 255, 255, 255;
                    --bg-secondary: #f8f9fa;
                    --bg-secondary-rgb: 248, 249, 250;
                    --bg-tertiary: #e9ecef;
                    --bg-tertiary-rgb: 233, 236, 239;
                    --border-color: #dee2e6;
                    --text-primary: #212529;
                    --text-secondary: #6c757d;
                    --text-muted: #999;
                    --accent-primary: #667eea;
                    --accent-secondary: #764ba2;
                    --gradient: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
                }
                
                /* Dark mode - multiple selectors for reliability */
                [data-theme="dark"],
                html[data-theme="dark"],
                .dark-theme,
                html.dark-theme {
                    --bg-primary: #1a1a2e;
                    --bg-primary-rgb: 26, 26, 46;
                    --bg-secondary: #16213e;
                    --bg-secondary-rgb: 22, 33, 62;
                    --bg-tertiary: #0f0f23;
                    --bg-tertiary-rgb: 15, 15, 35;
                    --border-color: #2a2a3e;
                    --text-primary: #fff;
                    --text-secondary: #999;
                    --text-muted: #666;
                    --accent-primary: #667eea;
                    --accent-secondary: #764ba2;
                    --gradient: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
                }
                
                /* Body styling */
                body {
                    background: var(--bg-tertiary) !important;
                    color: var(--text-primary) !important;
                    transition: background-color 0.3s ease, color 0.3s ease;
                }
                
                /* Ensure proper theme inheritance */
                [data-theme="dark"] body,
                html[data-theme="dark"] body,
                .dark-theme body,
                html.dark-theme body {
                    background: var(--bg-tertiary) !important;
                    color: var(--text-primary) !important;
                }
                
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: white;
                    color: var(--text-primary);
                    line-height: 1.6;
                    -webkit-font-smoothing: antialiased;
                    -moz-osx-font-smoothing: grayscale;
                    min-height: 100vh;
                    transition: background-color 0.3s, color 0.3s;
                }
                
                [data-theme="dark"] body {
                    background: #0f0f23 !important;
                }
                
                /* Ensure body background changes are applied */
                body[data-theme="light"] {
                    background: white !important;
                }
                
                body[data-theme="dark"] {
                    background: #0f0f23 !important;
                }
                
                /* Keep Bootstrap container defaults but ensure it's centered */
                .container {
                    margin: 0 auto;
                }
                
                a {
                    color: var(--accent-primary);
                    text-decoration: none;
                    transition: opacity 0.2s;
                }
                
                a:hover {
                    opacity: 0.8;
                }
                
                h1, h2, h3, h4, h5, h6 {
                    font-weight: 600;
                    line-height: 1.3;
                    margin-bottom: 1rem;
                    color: var(--text-primary);
                }
                
                /* Theme toggle button - now hidden as it's in header */
                .theme-toggle {
                    display: none;
                }
                
                /* Scrollbar */
                ::-webkit-scrollbar {
                    width: 10px;
                    height: 10px;
                }
                
                ::-webkit-scrollbar-track {
                    background: var(--bg-secondary);
                }
                
                ::-webkit-scrollbar-thumb {
                    background: var(--border-color);
                    border-radius: 5px;
                }
                
                ::-webkit-scrollbar-thumb:hover {
                    background: var(--text-muted);
                }
                
                /* Bootstrap overrides */
                .alert {
                    background: transparent;
                    border-color: var(--border-color);
                    color: var(--text-primary);
                }
                
                .btn {
                    transition: all 0.2s;
                }
                
                /* Remove old Bootstrap styles */
                .bg-light { background: transparent !important; }
                .bg-white { background: transparent !important; }
                .text-dark { color: var(--text-primary) !important; }
                .border { border-color: var(--border-color) !important; }
            </style>
HTML;

    // Add CSS files based on layout
    foreach ($layout['css'] as $cssFile) {
        echo "<link rel='stylesheet' type='text/css' href='{$baseUrl}css/{$cssFile}'>\n";
    }

    echo <<<HTML
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <title>
HTML;
    
    echo htmlspecialchars($optimizedTitle, ENT_QUOTES, 'UTF-8');
    
    echo <<<HTML
</title>
            $structuredData
HTML;

    // Add dark mode CSS if enabled
    if ($layout['darkMode']) {
        echo DarkModeManager::getDarkModeCSS();
    }

    // Add analytics scripts if enabled
    if ($layout['analytics']) {
        echo <<<HTML
            <!-- AdSense code snippet -->
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2363662533799826" crossorigin="anonymous"></script>
            <!-- Clarity -->
            <script type="text/javascript">
                (function(c,l,a,r,i,t,y){
                    c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                    t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                    y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
                })(window, document, "clarity", "script", "pmqwtsrnfg");
            </script>
HTML;
    }

    // Add TinyMCE for dashboard layouts
    if (isset($layout['tinymce']) && $layout['tinymce']) {
        echo <<<HTML
            <!-- Tinymce -->
            <script src="https://cdn.tiny.cloud/1/y4herhyxuwf9pi78y7tdxsrjpar8zqwxy7mn8vya74pjix2u/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
HTML;
    }

    echo <<<HTML
        </head>
        <body class='{$layout['bodyClass']}' data-theme="">
            <!-- Sync theme with HTML element -->
            <script>
                // Ensure body matches HTML theme
                (function() {
                    var htmlTheme = document.documentElement.getAttribute('data-theme');
                    if (htmlTheme) {
                        document.body.setAttribute('data-theme', htmlTheme);
                    }
                })();
            </script>
HTML;

    // Include header if enabled
    if ($layout['header']) {
        include "header.php";
    }

    // Main content container
    echo "<main class='{$layout['container']}'>";

    // Include messages unless specifically disabled
    if (!isset($layout['hideMessages']) || !$layout['hideMessages']) {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/messages/display-messages.php";
    }

    // Output content
    if (isset($additionalData['content'])) {
        echo $additionalData['content'];
    } else {
        include $mainContent;
    }

    echo "</main>";

    // Include footer if enabled
    if ($layout['footer']) {
        include "footer.php";
    }

    echo <<<HTML
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        
        <!-- Theme Toggle Script -->
        <script>
            // Theme toggle functionality - works with button in header
            document.addEventListener('DOMContentLoaded', function() {
                const themeToggle = document.getElementById('themeToggleHeader');
                const themeIcon = document.getElementById('themeIconHeader');
                const body = document.body;
                
                // Get current theme (already applied in body script)
                const currentTheme = localStorage.getItem('theme') || 'light';
                updateThemeIcon(currentTheme);
                
                // Toggle theme
                if (themeToggle) {
                    themeToggle.addEventListener('click', () => {
                        const currentTheme = localStorage.getItem('theme') || 'light';
                        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                        
                        console.log('Toggling from', currentTheme, 'to', newTheme);
                        
                        // Update localStorage
                        try {
                            localStorage.setItem('theme', newTheme);
                            console.log('localStorage updated to:', localStorage.getItem('theme'));
                        } catch (e) {
                            console.error('localStorage.setItem failed:', e);
                        }
                        
                        // Update both HTML and body
                        document.documentElement.setAttribute('data-theme', newTheme);
                        document.documentElement.className = newTheme + '-theme';
                        body.setAttribute('data-theme', newTheme);
                        
                        // Update CSS variables
                        if (newTheme === 'dark') {
                            document.documentElement.style.setProperty('--bg-primary', '#0f0f23');
                            document.documentElement.style.setProperty('--text-primary', '#fff');
                            body.style.backgroundColor = '#0f0f23';
                        } else {
                            document.documentElement.style.setProperty('--bg-primary', 'white');
                            document.documentElement.style.setProperty('--text-primary', '#212529');
                            body.style.backgroundColor = 'white';
                        }
                        
                        updateThemeIcon(newTheme);
                    });
                }
                
                function updateThemeIcon(theme) {
                    if (themeIcon) {
                        if (theme === 'dark') {
                            themeIcon.classList.remove('fa-moon');
                            themeIcon.classList.add('fa-sun');
                        } else {
                            themeIcon.classList.remove('fa-sun');
                            themeIcon.classList.add('fa-moon');
                        }
                    }
                }
            });
        </script>
HTML;

    // Add lazy loading for full layouts
    if ($layout['seo'] === 'full') {
        echo '<script src="/js/lazy-load.js" defer></script>';
    }

    // Add performance debug info
    echo PerformanceMonitor::renderDebugInfo();
    
    // Add dark mode script if enabled
    if ($layout['darkMode']) {
        echo DarkModeManager::getThemeScript();
    }

    echo <<<HTML
    </body>
    </html>
HTML;
}

// Backward compatibility functions
function renderUnifiedTemplate(
    $pageTitle,
    $mainContent,
    $additionalData = [],
    $metaD = "",
    $metaK = "",
    $table = '',
    $countField = '',
    $linkPrefix = '',
    $layoutType = 'default'
) {
    // Convert old layout types to new options
    $layoutOptions = [];
    
    switch($layoutType) {
        case 'dashboard':
            $layoutOptions = [
                'css' => ['styles.css', 'dashboard/dashboard.css'],
                'container' => 'dashboard-content',
                'robotsMeta' => 'noindex,nofollow',
                'analytics' => false,
                'tinymce' => true
            ];
            break;
        case 'auth':
            $layoutOptions = [
                'header' => false,
                'footer' => false,
                'robotsMeta' => 'noindex,nofollow',
                'analytics' => false,
                'darkMode' => false,
                'container' => 'd-flex justify-content-center align-items-center min-vh-100',
                'css' => ['styles.css', 'authorization.css'],
                'hideMessages' => true
            ];
            break;
        case 'minimal':
            $layoutOptions = [
                'header' => false,
                'footer' => true,
                'robotsMeta' => 'noindex,nofollow',
                'analytics' => false,
                'darkMode' => false,
                'container' => 'container',
                'css' => ['styles.css']
            ];
            break;
        case 'nofollow':
            $layoutOptions = [
                'robotsMeta' => 'noindex,nofollow',
                'analytics' => false,
                'darkMode' => false
            ];
            break;
        default:
            $layoutOptions = []; // Use defaults
    }
    
    renderTemplate($pageTitle, $mainContent, $additionalData, $metaD, $metaK, $table, $countField, $linkPrefix, $layoutOptions);
}

function renderTemplateOriginal(
    $pageTitle,
    $mainContent,
    $additionalData = [],
    $metaD = "",
    $metaK = "",
    $table = '',
    $countField = '',
    $linkPrefix = ''
) {
    renderTemplate($pageTitle, $mainContent, $additionalData, $metaD, $metaK, $table, $countField, $linkPrefix);
}
?>