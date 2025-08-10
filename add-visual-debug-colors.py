#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def upload_file(ftp, content, filename):
    with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
        tmp.write(content)
        tmp_path = tmp.name
    
    with open(tmp_path, 'rb') as file:
        ftp.storbinary(f'STOR {filename}', file)
    os.unlink(tmp_path)

def main():
    print("🎨 ADDING VISUAL DEBUG COLORS")
    print("RED header, GREEN main div, YELLOW footer")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Update header with RED background
        header_content = '''<?php
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
        
        /* DEBUG: RED HEADER */
        .navbar {
            background-color: #ff0000 !important; /* RED */
            border-bottom: 3px solid #cc0000;
        }
        
        .navbar-brand, .nav-link {
            color: white !important;
            font-weight: bold;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
        }
        
        .dropdown-menu {
            background-color: #ff6666;
        }
        
        .dropdown-item {
            color: white !important;
        }
        
        .dropdown-item:hover {
            background-color: #cc0000;
            color: white !important;
        }
        
        .dropdown:hover .dropdown-menu {
            display: block !important;
        }
        
        /* DEBUG: GREEN MAIN CONTENT */
        .container, main, .content, .main-content {
            background-color: #00ff00 !important; /* GREEN */
            border: 2px solid #00cc00;
            margin: 10px auto;
            padding: 20px;
        }
        
        .card {
            background-color: #66ff66 !important;
            border: 1px solid #00cc00 !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap me-2"></i>11klassniki.ru [RED HEADER]
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
    
    <!-- GREEN MAIN CONTENT WRAPPER -->
    <div class="main-content">'''
        
        upload_file(ftp, header_content, 'common-components/real_header.php')
        print("   ✅ Updated header with RED background")
        
        # 2. Update footer with YELLOW background
        footer_content = '''    </div> <!-- End main content wrapper -->
    
    <!-- DEBUG: YELLOW FOOTER -->
    <footer style="background-color: #ffff00 !important; color: black !important; padding: 2rem 0; margin-top: 2rem; border-top: 3px solid #cccc00;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color: black !important;">11klassniki.ru [YELLOW FOOTER]</h5>
                    <p style="color: black !important;">Портал образования России</p>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><a href="/news" style="color: black !important; text-decoration: underline;">Новости</a></li>
                        <li><a href="/schools-all-regions" style="color: black !important; text-decoration: underline;">Школы</a></li>
                        <li><a href="/vpo-all-regions" style="color: black !important; text-decoration: underline;">ВУЗы</a></li>
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
        
        upload_file(ftp, footer_content, 'common-components/real_footer.php')
        print("   ✅ Updated footer with YELLOW background")
        
        # 3. Update template (no changes needed, just for completeness)
        template_content = '''<?php
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_header.php';

// GREEN main content is now in header wrapper
echo $greyContent1 ?? '';
echo $greyContent2 ?? '';
echo $greyContent3 ?? '';
echo $greyContent4 ?? '';
echo $greyContent5 ?? '';
echo $greyContent6 ?? '';

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_footer.php';
?>'''
        
        upload_file(ftp, template_content, 'real_template.php')
        print("   ✅ Template ready (main content in GREEN wrapper)")
        
        ftp.quit()
        
        print(f"\n🎨 VISUAL DEBUG COLORS APPLIED!")
        
        print(f"\n🎯 Colors added:")
        print(f"🔴 HEADER: Bright red background with white text")
        print(f"🟢 MAIN DIV: Bright green background for all content")
        print(f"🟡 FOOTER: Bright yellow background with black text")
        
        print(f"\n🧪 Now all pages should show:")
        print(f"• RED header with '[RED HEADER]' label")
        print(f"• GREEN main content area")
        print(f"• YELLOW footer with '[YELLOW FOOTER]' label")
        
        print(f"\n🌐 Test pages:")
        print(f"• https://11klassniki.ru/")
        print(f"• https://11klassniki.ru/news")
        print(f"• https://11klassniki.ru/category/abiturientam")
        print(f"• https://11klassniki.ru/schools-all-regions")
        
        print(f"\n💡 Clear cache (Ctrl+Shift+R) to see bright colors!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()