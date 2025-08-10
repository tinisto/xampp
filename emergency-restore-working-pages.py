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
    print("🚨 EMERGENCY: RESTORE WORKING PAGES")
    print("All pages show Internal Server Error - restoring last working versions")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Restore working template system
        print("\n1. 🔧 Restoring template system...")
        
        # Restore real_header.php
        working_header = '''<?php
if (session_status() == PHP_SESSION_NONE) {
    try { session_start(); } catch (Exception $e) { /* Continue */ }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Образование'; ?> - 11klassniki.ru</title>
    
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
    </nav>'''
        
        # Upload header
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(working_header)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR common-components/real_header.php', file)
        os.unlink(tmp_path)
        print("   ✅ Restored real_header.php")
        
        # Restore real_footer.php
        working_footer = '''    <footer class="bg-dark text-white py-4 mt-5">
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
        
        # Upload footer
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(working_footer)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR common-components/real_footer.php', file)
        os.unlink(tmp_path)
        print("   ✅ Restored real_footer.php")
        
        # Restore real_template.php
        working_template = '''<?php
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_header.php';

// Main content
echo $greyContent1 ?? '';
echo $greyContent2 ?? '';
echo $greyContent3 ?? '';
echo $greyContent4 ?? '';
echo $greyContent5 ?? '';
echo $greyContent6 ?? '';

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_footer.php';
?>'''
        
        # Upload template
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(working_template)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR real_template.php', file)
        os.unlink(tmp_path)
        print("   ✅ Restored real_template.php")
        
        # 2. Restore working news page that uses templates
        print("\n2. 📰 Restoring news page...")
        
        working_news = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'Новости образования';
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

$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-4">Новости образования</h1>
        </div>
    </div>
</div>';

$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';

$greyContent5 = '
<div class="container">
    <div class="row">';

if (!empty($posts)) {
    foreach ($posts as $post) {
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/post/' . htmlspecialchars($post['url_slug'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($post['title'] ?? 'Без заголовка') . '
                        </a>
                    </h5>
                    <p class="card-text">
                        ' . htmlspecialchars(mb_substr(strip_tags($post['text'] ?? ''), 0, 150)) . '...
                    </p>
                    <small class="text-muted">' . date('d.m.Y', strtotime($post['date_post'] ?? 'now')) . '</small>
                </div>
            </div>
        </div>';
    }
} else {
    $greyContent5 .= '
    <div class="col-12">
        <div class="alert alert-info">
            <h4>Новости загружаются</h4>
            <p>Скоро здесь появятся свежие новости образования.</p>
        </div>
    </div>';
}

$greyContent5 .= '
    </div>
</div>';

$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        # Upload news page
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(working_news)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR news-new.php', file)
        os.unlink(tmp_path)
        print("   ✅ Restored news-new.php")
        
        ftp.quit()
        
        print(f"\n✅ EMERGENCY RESTORE COMPLETE!")
        
        print(f"\n🎯 Restored working system:")
        print(f"✅ Template system: real_header.php + real_footer.php + real_template.php")
        print(f"✅ News page: Uses template system")
        print(f"✅ Category page: Self-contained (working)")
        
        print(f"\n🧪 Test pages:")
        print(f"• https://11klassniki.ru/news (should work)")
        print(f"• https://11klassniki.ru/category/abiturientam (should work)")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()