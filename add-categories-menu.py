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
    print("🔧 ADDING CATEGORIES AS SEPARATE MENU ITEM")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Update header with separate Categories dropdown
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
        
        /* Fix dropdown visibility */
        .dropdown-menu {
            background-color: #ff6666 !important;
            display: none;
            position: absolute;
            z-index: 1000;
        }
        
        .dropdown-item {
            color: white !important;
        }
        
        .dropdown-item:hover {
            background-color: #cc0000 !important;
            color: white !important;
        }
        
        /* Show dropdown on hover AND click */
        .dropdown:hover .dropdown-menu,
        .dropdown-menu.show {
            display: block !important;
        }
        
        /* Make dropdown toggle more visible */
        .dropdown-toggle::after {
            color: white !important;
            border-top-color: white !important;
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
                    
                    <!-- EDUCATION DROPDOWN -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="educationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Образование
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="educationDropdown">
                            <li><a class="dropdown-item" href="/schools-all-regions">🏫 Школы</a></li>
                            <li><a class="dropdown-item" href="/vpo-all-regions">🎓 ВУЗы</a></li>
                            <li><a class="dropdown-item" href="/spo-all-regions">🏢 СПО</a></li>
                        </ul>
                    </li>
                    
                    <!-- CATEGORIES AS SEPARATE DROPDOWN -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Категории
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                            <li><a class="dropdown-item" href="/category/abiturientam">📚 Абитуриентам</a></li>
                            <li><a class="dropdown-item" href="/category/11-klassniki">👥 11-классники</a></li>
                            <li><a class="dropdown-item" href="/category/education-news">📰 Новости образования</a></li>
                            <li><a class="dropdown-item" href="/category/a-naposledok-ya-skazhu">💭 А напоследок я скажу</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="/search">Поиск</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0): ?>
                        <!-- User is logged in -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['username'] ?? 'Профиль'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/account">📊 Личный кабинет</a></li>
                                <li><a class="dropdown-item" href="/account/edit-profile">✏️ Редактировать</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout">🚪 Выйти</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- User is not logged in -->
                        <li class="nav-item">
                            <a class="nav-link" href="/login">
                                <i class="fas fa-sign-in-alt me-1"></i>Вход
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/registration">
                                <i class="fas fa-user-plus me-1"></i>Регистрация
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Ensure Bootstrap JavaScript is loaded for dropdowns -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Additional dropdown fix -->
    <script>
        // Ensure dropdowns work on both hover and click
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.dropdown');
            
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                // Show on hover
                dropdown.addEventListener('mouseenter', function() {
                    if (menu) {
                        menu.classList.add('show');
                        toggle.setAttribute('aria-expanded', 'true');
                    }
                });
                
                dropdown.addEventListener('mouseleave', function() {
                    if (menu) {
                        menu.classList.remove('show');
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
                
                // Also work on click
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    menu.classList.toggle('show');
                });
            });
        });
    </script>
    
    <!-- GREEN MAIN CONTENT WRAPPER -->
    <div class="main-content">'''
        
        upload_file(ftp, header_content, 'common-components/real_header.php')
        print("   ✅ Added 'Категории' as separate menu item")
        
        ftp.quit()
        
        print("\n✅ CATEGORIES MENU ADDED!")
        
        print("\n🎯 Navigation structure now:")
        print("• Главная")
        print("• Новости")
        print("• Образование (dropdown)")
        print("  - 🏫 Школы")
        print("  - 🎓 ВУЗы")
        print("  - 🏢 СПО")
        print("• Категории (dropdown)")
        print("  - 📚 Абитуриентам")
        print("  - 👥 11-классники")
        print("  - 📰 Новости образования")
        print("  - 💭 А напоследок я скажу")
        print("• Поиск")
        
        print("\n🧪 Categories are now a separate dropdown menu!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()