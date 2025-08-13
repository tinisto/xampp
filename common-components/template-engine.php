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

// Unified template function with layout options
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
    global $connection, $baseUrl;
    
    // Define layout configurations
    $layouts = [
        'default' => [
            'header' => true,
            'footer' => true,
            'seo' => 'full',
            'analytics' => true,
            'darkMode' => true,
            'container' => 'container my-3',
            'bodyClass' => 'full-height-flex',
            'css' => ['styles.css', 'post-styles.css', 'buttons-styles.css', 'test.css', 'dark-mode-fix.css']
        ],
        'dashboard' => [
            'header' => true,
            'footer' => true,
            'seo' => 'noindex',
            'analytics' => false,
            'darkMode' => false,
            'container' => 'dashboard-content',
            'bodyClass' => 'full-height-flex',
            'css' => ['styles.css', 'dashboard/dashboard.css']
        ],
        'auth' => [
            'header' => false,
            'footer' => false,
            'seo' => 'noindex',
            'analytics' => false,
            'darkMode' => false,
            'container' => 'd-flex justify-content-center align-items-center min-vh-100',
            'bodyClass' => 'full-height-flex',
            'css' => ['styles.css', 'authorization.css']
        ],
        'minimal' => [
            'header' => false,
            'footer' => true,
            'seo' => 'noindex',
            'analytics' => false,
            'darkMode' => false,
            'container' => 'container',
            'bodyClass' => 'full-height-flex',
            'css' => ['styles.css']
        ],
        'search' => [
            'header' => true,
            'footer' => true,
            'seo' => 'full',
            'analytics' => true,
            'darkMode' => false,
            'container' => 'container my-3',
            'bodyClass' => 'full-height-flex',
            'css' => ['styles.css', 'post-styles.css', 'buttons-styles.css', 'test.css', 'dark-mode-fix.css']
        ],
        'nofollow' => [
            'header' => true,
            'footer' => true,
            'seo' => 'nofollow',
            'analytics' => false,
            'darkMode' => false,
            'container' => 'container my-3',
            'bodyClass' => 'full-height-flex',
            'css' => ['styles.css', 'post-styles.css', 'buttons-styles.css', 'test.css', 'dark-mode-fix.css']
        ]
    ];
    
    $layout = $layouts[$layoutType] ?? $layouts['default'];
    
    // Set security headers
    setSecurityHeaders();
    
    // Prepare SEO based on layout type
    $seoOptions = [];
    if ($layout['seo'] === 'noindex' || $layout['seo'] === 'nofollow') {
        $seoOptions['robots'] = $layout['seo'] === 'noindex' ? 'noindex, nofollow' : 'noindex, nofollow';
    }
    
    if ($metaD && $layout['seo'] !== 'noindex') {
        $seoOptions['description'] = is_array($metaD) ? implode(", ", $metaD) : $metaD;
    }
    if ($metaK && $layout['seo'] !== 'noindex') {
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
HTML;

    if ($layout['seo'] === 'full') {
        echo <<<HTML
            <!-- Adsense Meta tag -->
            <meta name="google-adsense-account" content="ca-pub-2363662533799826">
            <link rel='canonical' href='" . getCurrentUrl() . "'>
HTML;
    }

    echo <<<HTML
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
HTML;

    // Add CSS files based on layout
    foreach ($layout['css'] as $cssFile) {
        echo "<link rel='stylesheet' type='text/css' href='{$baseUrl}css/{$cssFile}'>\n";
    }

    echo <<<HTML
            <link rel='icon' href='/favicon.ico' type='image/x-icon'>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

    // Add TinyMCE for dashboard
    if ($layoutType === 'dashboard') {
        echo <<<HTML
            <!-- Tinymce -->
            <script src="https://cdn.tiny.cloud/1/y4herhyxuwf9pi78y7tdxsrjpar8zqwxy7mn8vya74pjix2u/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
HTML;
    }

    echo <<<HTML
        </head>
        <body class='{$layout['bodyClass']}'>
HTML;

    // Include header if enabled
    if ($layout['header']) {
        include "header.php";
    }

    // Main content container
    echo "<main class='{$layout['container']}'>";

    // Include messages for non-auth layouts
    if ($layoutType !== 'auth') {
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

// Backward compatibility function - redirects to unified template
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
    renderUnifiedTemplate($pageTitle, $mainContent, $additionalData, $metaD, $metaK, $table, $countField, $linkPrefix, 'default');
}
?>