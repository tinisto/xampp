<?php
// Simple 404 page without construction check
$pageTitle = 'Страница не найдена';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - 11 Классники</title>
    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .error-container {
            text-align: center;
            padding: 100px 20px;
            min-height: 60vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #28a745;
            margin: 0;
        }
        .error-message {
            font-size: 24px;
            margin: 20px 0;
        }
        .back-home {
            display: inline-block;
            padding: 10px 30px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php 
    // Include header safely
    $headerFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php';
    if (file_exists($headerFile)) {
        @include $headerFile;
    }
    ?>
    
    <div class="error-container">
        <h1 class="error-code">404</h1>
        <p class="error-message">Страница не найдена</p>
        <p>Запрашиваемая страница не существует или была удалена.</p>
        <a href="/" class="back-home">Вернуться на главную</a>
    </div>
    
    <?php 
    // Include footer
    $footerFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php';
    if (file_exists($footerFile)) {
        @include $footerFile;
    }
    ?>
</body>
</html>