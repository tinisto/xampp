<?php
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/redirectToErrorPage.php";

$baseUrl = '/';

function renderTemplate($pageTitle, $mainContent, $additionalData = [], $metaD = '', $metaK = '')
{
    global $connection, $baseUrl;

    $metaDescription = is_array($metaD) ? implode(', ', $metaD) : $metaD;
    $metaDescription = $metaDescription ? "<meta name='description' content='$metaDescription'>" : '';
    $metaKeywords = is_array($metaK) ? implode(', ', $metaK) : $metaK;
    $metaKeywords = $metaKeywords ? "<meta name='keywords' content='$metaKeywords'>" : '';

    echo <<<HTML
        <!DOCTYPE html>
        <html lang='ru'>
        <head>
            <!-- Adsense Meta tag -->
            <meta name="google-adsense-account" content="ca-pub-2363662533799826">  
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            $metaDescription
            $metaKeywords
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
            <link rel='stylesheet' type='text/css' href='{$baseUrl}css/styles.css'>
            <link rel='stylesheet' type='text/css' href='{$baseUrl}css/post-styles.css'>
            <link rel='stylesheet' type='text/css' href='{$baseUrl}css/buttons-styles.css'>
            <link rel='icon' href='/favicon.ico' type='image/x-icon'>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <title>$pageTitle</title>
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
        </head>
        HTML;

    include 'header.php';

    echo <<<HTML
        <body class='full-height-flex bg-secondary'>

<main class='container my-3'>
HTML;

    include $mainContent;

    echo <<<HTML
            </main>
HTML;

    include 'footer.php';

    echo <<<HTML
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
             
        </body>
        </html>
HTML;
}
