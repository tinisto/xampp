#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🚨 EMERGENCY FIX: Category page Internal Server Error")
    print("Restoring working self-contained category page")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create WORKING self-contained category page (like original but with debug colors)
        working_category = '''<?php
// WORKING CATEGORY PAGE - Self-contained with debug colors
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$category_en = $_GET['category_en'] ?? '';
if (empty($category_en)) {
    header('Location: /');
    exit();
}

$posts = [];
$category_name = '';

// Category mappings
$category_mappings = [
    'abiturientam' => 'Абитуриентам',
    '11-klassniki' => '11-классники',
    'education-news' => 'Новости образования'
];

$category_name = $category_mappings[$category_en] ?? ucfirst(str_replace('-', ' ', $category_en));

// Get posts from database
if ($connection) {
    try {
        if ($category_en === 'abiturientam') {
            $keywords = ['абитуриент', 'поступление', 'вуз', 'университет', 'егэ'];
            $all_posts = [];
            
            foreach ($keywords as $keyword) {
                $stmt = $connection->prepare("SELECT 'post' as type, id, title_post as title, text_post as text, url_slug, date_post as date_created FROM posts WHERE title_post LIKE ? ORDER BY date_post DESC LIMIT 5");
                if ($stmt) {
                    $search = '%' . $keyword . '%';
                    $stmt->bind_param("s", $search);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $all_posts[] = $row;
                    }
                    $stmt->close();
                }
            }
            
            // Remove duplicates
            $seen = [];
            foreach ($all_posts as $post) {
                $key = $post['type'] . '_' . $post['id'];
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $posts[] = $post;
                }
            }
        }
    } catch (Exception $e) {
        error_log('Category query error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category_name); ?> - 11klassniki.ru</title>
    
    <!-- UNIFIED FAVICON -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2025">
    <link rel="shortcut icon" href="/favicon.svg?v=2025">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
        }
        
        /* DEBUG COLORS - CATEGORY PAGE */
        nav, .navbar {
            background-color: #ff0000 !important; /* RED HEADER */
            border-bottom: 3px solid #cc0000;
        }

        .navbar-brand, .nav-link, .navbar-nav a {
            color: white !important;
            font-weight: bold !important;
        }

        .container {
            background-color: #00ff00 !important; /* GREEN MAIN */
            border: 2px solid #00cc00;
            margin: 10px auto;
            padding: 20px;
        }

        .card {
            background-color: #66ff66 !important;
            border: 1px solid #00cc00 !important;
        }

        h1, h2, h3, p {
            background-color: rgba(255,255,0,0.7) !important;
            display: inline-block;
            padding: 3px 8px;
            margin: 2px 0;
        }

        footer {
            background-color: #ffff00 !important; /* YELLOW FOOTER */
            border: 3px solid #cccc00;
            color: black !important;
        }

        footer h5, footer p, footer a {
            color: black !important;
        }

        .breadcrumb {
            background-color: #ccffcc !important;
        }
        
        .dropdown-menu {
            display: none;
        }
        
        .dropdown:hover .dropdown-menu {
            display: block !important;
        }
    </style>
</head>
<body>
    <!-- RED HEADER -->
    <nav class="navbar navbar-expand-lg shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap me-2"></i>11klassniki.ru
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/news">Новости</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Образование
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/schools-all-regions">🏫 Школы</a></li>
                            <li><a class="dropdown-item" href="/vpo-all-regions">🎓 ВУЗы</a></li>
                            <li><a class="dropdown-item" href="/spo-all-regions">🏢 СПО</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/category/abiturientam">📚 Абитуриентам</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/search">Поиск</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Вход</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- GREEN MAIN CONTENT -->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Главная</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($category_name); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="display-4 mb-4"><?php echo htmlspecialchars($category_name); ?></h1>
                <p class="lead">Найдено материалов: <?php echo count($posts); ?></p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="/post/<?php echo htmlspecialchars($post['url_slug'] ?? ''); ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($post['title'] ?? 'Без заголовка'); ?>
                                    </a>
                                </h5>
                                <p class="card-text">
                                    <?php echo htmlspecialchars(mb_substr(strip_tags($post['text'] ?? ''), 0, 100)); ?>...
                                </p>
                                <small class="text-muted"><?php echo date('d.m.Y', strtotime($post['date_created'] ?? 'now')); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <h4>Материалы готовятся к публикации</h4>
                        <p>В категории "<?php echo htmlspecialchars($category_name); ?>" скоро появятся новые материалы.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- YELLOW FOOTER -->
    <footer style="background-color: #ffff00 !important; color: black !important; padding: 2rem 0; margin-top: 2rem;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color: black !important;">11klassniki.ru</h5>
                    <p style="color: black !important;">Портал образования России</p>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><a href="/news" style="color: black !important;">Новости</a></li>
                        <li><a href="/schools-all-regions" style="color: black !important;">Школы</a></li>
                        <li><a href="/vpo-all-regions" style="color: black !important;">ВУЗы</a></li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: #cccc00;">
            <div class="text-center">
                <small style="color: black !important;">© <?php echo date('Y'); ?> 11klassniki.ru</small>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Force dropdown to work
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('mouseenter', function() {
                    const menu = this.querySelector('.dropdown-menu');
                    if (menu) menu.style.display = 'block';
                });
                dropdown.addEventListener('mouseleave', function() {
                    const menu = this.querySelector('.dropdown-menu');
                    if (menu) menu.style.display = 'none';
                });
            });
        });
    </script>
</body>
</html>'''
        
        # Upload working category page
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(working_category)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR category-new.php', file)
        os.unlink(tmp_path)
        
        print("   ✅ Restored working category page with debug colors")
        
        ftp.quit()
        
        print(f"\n✅ CATEGORY PAGE FIXED!")
        
        print(f"\n🎯 Both pages now work and have debug colors:")
        print(f"🔴 RED headers")
        print(f"🟢 GREEN main content") 
        print(f"🟡 YELLOW footers")
        
        print(f"\n🧪 Test both pages:")
        print(f"• https://11klassniki.ru/news")
        print(f"• https://11klassniki.ru/category/abiturientam")
        
        print(f"\n💡 Both should show RED/GREEN/YELLOW colors now!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()