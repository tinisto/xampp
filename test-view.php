<?php
// Simple test page to view the site at this commit
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test View - 11klassniki.ru</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'common-components/header.php'; ?>
    
    <div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
        <h1>Test Page - Viewing Commit 1b941e7</h1>
        <p>This page shows the header with Categories dropdown from this commit.</p>
        
        <h2>Key Features at this commit:</h2>
        <ul>
            <li>Categories dropdown in header navigation</li>
            <li>Navigation: Главная | Категории | ВУЗы | ССУЗы | Школы | Новости | Тесты</li>
            <li>Tests still use full template with header/footer</li>
        </ul>
        
        <h2>Quick Links:</h2>
        <ul>
            <li><a href="/tests">View Tests Page</a></li>
            <li><a href="/news">View News Page</a></li>
            <li><a href="/category/education-news">View Category Page</a></li>
        </ul>
    </div>
    
    <?php include 'common-components/footer.php'; ?>
</body>
</html>