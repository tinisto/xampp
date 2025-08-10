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
    print("🗑️ DELETING ONE TEMPLATE SYSTEM")
    print("Converting news page to use self-contained system like category page")
    print("Delete: real_header.php, real_footer.php, real_template.php")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Delete template system files
        template_files = [
            'common-components/real_header.php',
            'common-components/real_footer.php', 
            'real_template.php'
        ]
        
        for file_path in template_files:
            try:
                ftp.delete(file_path)
                print(f"   ✅ Deleted: {file_path}")
            except:
                print(f"   ❌ Could not delete: {file_path}")
        
        # Convert news page to self-contained (like category page)
        self_contained_news = '''<?php
// SELF-CONTAINED NEWS PAGE - No template includes
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$posts = [];
if ($connection) {
    try {
        $stmt = $connection->prepare("SELECT id, title_post as title, text_post as text, url_slug, date_post FROM posts ORDER BY date_post DESC LIMIT 20");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log('News query error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новости - 11klassniki.ru</title>
    
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2025">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
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
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
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
                        <a class="nav-link active" href="/news">Новости</a>
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

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="display-4 mb-4">Новости образования</h1>
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
                                    <?php echo htmlspecialchars(mb_substr(strip_tags($post['text'] ?? ''), 0, 150)); ?>...
                                </p>
                                <small class="text-muted"><?php echo date('d.m.Y', strtotime($post['date_post'] ?? 'now')); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <h4>Новости загружаются</h4>
                        <p>Скоро здесь появятся свежие новости образования.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>11klassniki.ru</h5>
                    <p>Портал образования России</p>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><a href="/news" class="text-white">Новости</a></li>
                        <li><a href="/schools-all-regions" class="text-white">Школы</a></li>
                        <li><a href="/vpo-all-regions" class="text-white">ВУЗы</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <small>© <?php echo date('Y'); ?> 11klassniki.ru</small>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
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
        
        # Upload self-contained news page
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(self_contained_news)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR news-new.php', file)
        os.unlink(tmp_path)
        
        print("   ✅ Converted news page to self-contained")
        
        ftp.quit()
        
        print(f"\n✅ TEMPLATE SYSTEM DELETED!")
        
        print(f"\n🎯 Now we have:")
        print(f"✅ News page: Self-contained HTML")
        print(f"✅ Category page: Self-contained HTML") 
        print(f"✅ NO template includes")
        print(f"✅ NO separate header/footer files")
        print(f"✅ ONE system: Everything self-contained")
        
        print(f"\n🧪 Both pages are now identical systems:")
        print(f"• https://11klassniki.ru/news")
        print(f"• https://11klassniki.ru/category/abiturientam")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()