<?php
// Simple working index for commit 1b941e7
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>11klassniki.ru - Educational Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            min-height: 400px;
        }
        .navigation-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 40px 0;
        }
        .nav-card {
            background: #28a745;
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .nav-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .nav-card h3 {
            margin: 10px 0;
        }
        .nav-card i {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'common-components/header.php'; ?>
    
    <div class="main-content">
        <h1>Welcome to 11klassniki.ru - Commit 1b941e7</h1>
        <p>This version has Categories dropdown in the header navigation.</p>
        
        <div class="navigation-grid">
            <a href="/vpo-all-regions" class="nav-card">
                <i class="fas fa-university"></i>
                <h3>ВУЗы</h3>
                <p>Universities by regions</p>
            </a>
            
            <a href="/spo-all-regions" class="nav-card">
                <i class="fas fa-school"></i>
                <h3>ССУЗы</h3>
                <p>Colleges by regions</p>
            </a>
            
            <a href="/schools-all-regions" class="nav-card">
                <i class="fas fa-graduation-cap"></i>
                <h3>Школы</h3>
                <p>Schools by regions</p>
            </a>
            
            <a href="/news" class="nav-card">
                <i class="fas fa-newspaper"></i>
                <h3>Новости</h3>
                <p>Education news</p>
            </a>
            
            <a href="/tests" class="nav-card">
                <i class="fas fa-clipboard-list"></i>
                <h3>Тесты</h3>
                <p>Online tests</p>
            </a>
            
            <a href="/category/education-news" class="nav-card">
                <i class="fas fa-folder"></i>
                <h3>Категории</h3>
                <p>Browse by category</p>
            </a>
        </div>
    </div>
    
    <?php include 'common-components/footer-unified.php'; ?>
</body>
</html>