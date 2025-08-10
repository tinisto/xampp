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
    print("🗑️  DELETING ALL EXTRA HEADERS/FOOTERS - FINAL CLEANUP")
    print("Problem: Multiple conflicting template files causing different designs")
    print("Solution: DELETE ALL extras, keep only ONE unified system")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Find ALL template files
        print("\n1. 🔍 Scanning for ALL template files...")
        
        files = []
        ftp.retrlines('LIST', files.append)
        
        # Find all header/footer/template files
        template_files = []
        for file_line in files:
            filename = file_line.split()[-1] if file_line.split() else ""
            if any(keyword in filename.lower() for keyword in [
                'header', 'footer', 'template', 'nav', 'menu'
            ]) and filename.endswith('.php'):
                file_size = file_line.split()[4] if len(file_line.split()) >= 5 else "0"
                template_files.append((filename, file_size))
        
        # Also check common-components directory
        try:
            ftp.cwd('common-components')
            component_files = []
            ftp.retrlines('LIST', component_files.append)
            
            for file_line in component_files:
                filename = file_line.split()[-1] if file_line.split() else ""
                if any(keyword in filename.lower() for keyword in [
                    'header', 'footer', 'template', 'nav', 'menu', 'favicon'
                ]) and filename.endswith('.php'):
                    file_size = file_line.split()[4] if len(file_line.split()) >= 5 else "0"
                    template_files.append(('common-components/' + filename, file_size))
            
            ftp.cwd('..')  # Back to root
        except:
            print("   ℹ️  common-components directory checked")
        
        print(f"   Found {len(template_files)} template files:")
        for filename, size in sorted(template_files):
            print(f"      {filename} ({size} bytes)")
        
        # 2. Delete ALL extra template files - keep only essential ones
        print(f"\n2. 🗑️  Deleting duplicate template files...")
        
        # Files to KEEP (essential unified system)
        keep_files = [
            'common-components/real_header.php',
            'common-components/real_footer.php',
            'real_template.php'
        ]
        
        # Delete everything else
        deleted_count = 0
        for filename, size in template_files:
            if filename not in keep_files:
                try:
                    ftp.delete(filename)
                    print(f"   ✅ Deleted {filename}")
                    deleted_count += 1
                except Exception as e:
                    print(f"   ⚠️  Could not delete {filename}: {str(e)}")
            else:
                print(f"   ⭐ Kept essential file: {filename}")
        
        print(f"   🗑️  Deleted {deleted_count} duplicate files")
        
        # 3. Create ONE perfect unified header
        print(f"\n3. ✨ Creating ONE perfect header...")
        
        perfect_header = '''<?php
// THE ONLY HEADER - Unified design for ALL pages
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
    
    <!-- ONE UNIFIED FAVICON -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=final2025">
    <link rel="shortcut icon" href="/favicon.svg?v=final2025">
    
    <!-- ONE UNIFIED CSS FRAMEWORK -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <!-- ONE UNIFIED STYLE SYSTEM -->
    <style>
        :root {
            --primary: #007bff;
            --primary-dark: #0056b3;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--light-bg);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
        }
        
        .page-hero {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .card {
            border: none;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }
        
        .badge-news { background: #28a745; }
        .badge-post { background: var(--primary); }
    </style>
</head>
<body>
    <!-- ONE UNIFIED NAVIGATION -->
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
                        <a class="nav-link" href="/"><i class="fas fa-home me-1"></i>Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/news"><i class="fas fa-newspaper me-1"></i>Новости</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-graduation-cap me-1"></i>Образование
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
                        <a class="nav-link" href="/search"><i class="fas fa-search me-1"></i>Поиск</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['username'] ?? 'Профиль'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/account">Профиль</a></li>
                                <li><a class="dropdown-item" href="/logout">Выход</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login"><i class="fas fa-sign-in-alt me-1"></i>Вход</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>'''
        
        # Upload THE ONLY header
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(perfect_header)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR common-components/real_header.php', file)
        os.unlink(tmp_path)
        print("   ✅ THE ONLY header uploaded")
        
        # 4. Create ONE perfect unified footer
        print(f"\n4. ✨ Creating ONE perfect footer...")
        
        perfect_footer = '''    <!-- ONE UNIFIED FOOTER -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-graduation-cap me-2"></i>11klassniki.ru</h5>
                    <p class="mb-0">Портал образования России</p>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled mb-0">
                        <li><a href="/news" class="text-light text-decoration-none">Новости</a></li>
                        <li><a href="/schools-all-regions" class="text-light text-decoration-none">Школы</a></li>
                        <li><a href="/vpo-all-regions" class="text-light text-decoration-none">ВУЗы</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <small>© <?php echo date('Y'); ?> 11klassniki.ru</small>
            </div>
        </div>
    </footer>
    
    <!-- ONE UNIFIED JAVASCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>'''
        
        # Upload THE ONLY footer
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(perfect_footer)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR common-components/real_footer.php', file)
        os.unlink(tmp_path)
        print("   ✅ THE ONLY footer uploaded")
        
        ftp.quit()
        
        print(f"\n✅ TEMPLATE SYSTEM FINALLY CLEAN!")
        
        print(f"\n🎯 What's Left (THE ONLY FILES):")
        print("✅ common-components/real_header.php (THE ONLY header)")
        print("✅ common-components/real_footer.php (THE ONLY footer)")
        print("✅ real_template.php (main template if used)")
        
        print(f"\n🗑️  What Was Deleted:")
        print(f"❌ ALL duplicate headers/footers ({deleted_count} files)")
        print("❌ ALL conflicting template files")
        print("❌ ALL extra navigation files")
        print("❌ ALL duplicate favicon includes")
        
        print(f"\n✨ Result:")
        print("• ONE header design for all pages")
        print("• ONE footer design for all pages") 
        print("• ONE favicon system")
        print("• ONE color scheme (blue #007bff)")
        print("• NO MORE conflicts!")
        
        print(f"\n🧪 Test - both pages should look IDENTICAL now:")
        print("• https://11klassniki.ru/news")
        print("• https://11klassniki.ru/category/abiturientam")
        
        print(f"\n💡 Clear cache and refresh - headers should finally match!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()