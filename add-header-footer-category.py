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
    print("🔧 Adding proper header/footer to category page...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Download current working category page
        print("📥 Downloading current category page...")
        current_content = []
        ftp.retrlines('RETR category-new.php', current_content.append)
        print(f"Downloaded category-new.php ({len(current_content)} lines)")
        
        # Find where content starts (after PHP logic)
        content_start = 0
        for i, line in enumerate(current_content):
            if any(tag in line for tag in ['<!DOCTYPE', '<html', '<body']):
                content_start = i
                break
        
        # Extract PHP logic (everything before HTML)
        php_logic = current_content[:content_start]
        
        print(f"Found PHP logic: {len(php_logic)} lines")
        
        # Create new version with proper header/footer includes
        new_category = ""
        
        # Add PHP logic
        for line in php_logic:
            new_category += line + "\n"
        
        # Add header include
        new_category += "\n// Include site header\n"
        new_category += "include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_header.php';\n"
        new_category += "?>\n\n"
        
        # Add page content
        new_category += '''<!-- Category Page Content -->
<div class="container mt-4 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($category_name); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-12">
            <h1 class="h2 mb-4"><?php echo htmlspecialchars($category_name); ?></h1>
            
            <?php if (!empty($posts)): ?>
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Найдено материалов: <strong><?php echo count($posts); ?></strong>
                </div>
                
                <div class="row">
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-light">
                                    <span class="badge <?php echo $post['type'] === 'news' ? 'bg-success' : 'bg-primary'; ?>">
                                        <?php echo $post['type'] === 'news' ? 'Новость' : 'Статья'; ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php
                                        $url = $post['type'] === 'news' ? '/news/' . $post['url_slug'] : '/post/' . $post['url_slug'];
                                        ?>
                                        <a href="<?php echo htmlspecialchars($url); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <?php 
                                        $text = strip_tags($post['text']);
                                        echo htmlspecialchars(mb_substr($text, 0, 150));
                                        if (mb_strlen($text) > 150) echo '...';
                                        ?>
                                    </p>
                                </div>
                                <div class="card-footer text-muted">
                                    <small>
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('d.m.Y', strtotime($post['date_created'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <h4 class="alert-heading">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Материалы готовятся к публикации
                    </h4>
                    <p class="mb-3">В этом разделе скоро появятся полезные материалы для <?php echo mb_strtolower($category_name); ?>.</p>
                    <hr>
                    <div class="d-flex gap-2">
                        <a href="/news" class="btn btn-primary">
                            <i class="fas fa-newspaper me-1"></i>Читать новости
                        </a>
                        <a href="/search" class="btn btn-outline-primary">
                            <i class="fas fa-search me-1"></i>Поиск по сайту
                        </a>
                        <a href="/" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-1"></i>На главную
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include site footer
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_footer.php';
?>'''
        
        # Upload the updated category page
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(new_category)
            tmp_path = tmp.name
        
        print("📤 Uploading category page with header/footer...")
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR category-new.php', file)
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print("\n✅ Header/footer added to category page!")
        print("\n🔧 What was added:")
        print("• Site header include: real_header.php")
        print("• Site footer include: real_footer.php")
        print("• Proper breadcrumb navigation")
        print("• Bootstrap card layout for posts")
        print("• FontAwesome icons for better UX")
        print("• Enhanced empty state with multiple action buttons")
        
        print("\n🎯 Category page now includes:")
        print("✅ Full site navigation (header)")
        print("✅ Proper footer with site links")  
        print("✅ Consistent styling with rest of site")
        print("✅ Breadcrumb navigation")
        print("✅ Responsive card layout")
        print("✅ Professional empty state")
        
        print("\n🧪 Test the updated page:")
        print("https://11klassniki.ru/category/abiturientam")
        print("(Should now have proper header and footer like other site pages)")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()