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
    global $connection, $baseUrl;
    
    // Set security headers at the beginning
    setSecurityHeaders();
    
    // Prepare SEO meta tags
    $seoOptions = [];
    if ($metaD) {
        $seoOptions['description'] = is_array($metaD) ? implode(", ", $metaD) : $metaD;
    }
    if ($metaK) {
        $seoOptions['keywords'] = is_array($metaK) ? implode(", ", $metaK) : $metaK;
    }
    
    // Optimize page title for SEO
    $optimizedTitle = optimizeTitle($pageTitle);
    $seoOptions['title'] = $optimizedTitle;
    
    // Generate meta tags
    $metaTags = generateMetaTags($seoOptions);
    
    // Generate structured data
    $structuredData = generateStructuredData();

    echo <<<HTML
        <!DOCTYPE html>
        <html lang='ru'>
        <head>
            <!-- Adsense Meta tag -->
            <meta name="google-adsense-account" content="ca-pub-2363662533799826">
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            $metaTags
            <link rel='canonical' href='" . getCurrentUrl() . "'>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
            <link rel='stylesheet' type='text/css' href='{$baseUrl}css/styles.css'>
            <link rel='stylesheet' type='text/css' href='{$baseUrl}css/post-styles.css'>
            <link rel='stylesheet' type='text/css' href='{$baseUrl}css/buttons-styles.css'>
            <link rel='stylesheet' type='text/css' href='{$baseUrl}css/test.css'>
            <link rel='stylesheet' type='text/css' href='{$baseUrl}css/dark-mode-fix.css'>
            <link rel='icon' href='/favicon.ico' type='image/x-icon'>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <title>" . htmlspecialchars($optimizedTitle, ENT_QUOTES, 'UTF-8') . "</title>
            $structuredData" . 
            DarkModeManager::getDarkModeCSS()
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
            <!-- Tinymce -->
            <script src="https://cdn.tiny.cloud/1/y4herhyxuwf9pi78y7tdxsrjpar8zqwxy7mn8vya74pjix2u/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        </head>
HTML;

    include "header.php";

    echo <<<HTML
        <body class='full-height-flex'>
HTML;

    echo <<<HTML
<main class='container my-3'>
HTML;

    require_once $_SERVER["DOCUMENT_ROOT"] .
        "/includes/messages/display-messages.php";

    // Output the additional data content
    if (isset($additionalData['content'])) {
        echo $additionalData['content'];
    } else {
        include $mainContent;
    }

    echo <<<HTML
    </main>
HTML;

    include "footer.php";

    echo <<<HTML
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <script src="/js/lazy-load.js" defer></script>
HTML;

    // Add performance debug info if enabled
    echo PerformanceMonitor::renderDebugInfo();
    
    // Add dark mode script
    echo DarkModeManager::getThemeScript();

    echo <<<HTML
    </body>
    </html>
HTML;
}
?>
