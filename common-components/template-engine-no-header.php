<?php
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/redirectToErrorPage.php";

$baseUrl = '/';

function renderTemplate($pageTitle, $mainContent, $additionalData = [])
{
    global $connection, $baseUrl;

    echo <<<HTML
        <!DOCTYPE html>
        <html lang='ru'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <meta name="robots" content="noindex, nofollow">
            <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
            <link rel='stylesheet' type='text/css' href='{$baseUrl}css/styles.css'>
            <link rel='icon' href='/favicon.ico' type='image/x-icon'>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <title>$pageTitle</title>
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

    echo <<<HTML
        <body class='full-height-flex'>
HTML;

    include $mainContent;

    echo <<<HTML



HTML;


    include 'footer.php';

    echo <<<HTML
HTML;

    echo <<<HTML
            <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
        </body>
        </html>
HTML;
}
