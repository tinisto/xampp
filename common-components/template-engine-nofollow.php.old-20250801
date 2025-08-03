<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/redirectToErrorPage.php";

$baseUrl = '/';

function renderTemplate(
    $pageTitle,
    $mainContent
) {
    global $connection, $baseUrl;

    echo <<<HTML
        <!DOCTYPE html>
        <html lang='ru'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <meta name="robots" content="noindex, nofollow">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
            <link rel='stylesheet' type='text/css' href='{$baseUrl}css/styles.css'>
            <link rel='stylesheet' type='text/css' href='{$baseUrl}css/buttons-styles.css'>
            <link rel='icon' href='/favicon.ico' type='image/x-icon'>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <title>$pageTitle</title>
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
    include $mainContent;

    echo <<<HTML
    </main>
    
HTML;

    include "footer.php";

    echo <<<HTML
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    </body>
    </html>
HTML;
}
