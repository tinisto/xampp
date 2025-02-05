<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/redirectToErrorPage.php";

$baseUrl = '/';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel='stylesheet' type='text/css' href='{$baseUrl}css/styles.css'>
    <link rel='stylesheet' type='text/css' href='/css/authorization.css'>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>
        <?php echo $pageTitle; ?>
    </title>
</head>

<body class="full-height-flex">
    <main class=" container">
        <div class="d-flex justify-content-center align-items-center min-vh-100">
            <?php include $mainContent; ?>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>