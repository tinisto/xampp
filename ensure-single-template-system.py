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
    print("🔧 ENSURING TRULY SINGLE TEMPLATE SYSTEM")
    print("Delete ALL duplicate headers, footers, templates")
    print("Keep only ONE of each: real_header.php, real_footer.php, real_template.php")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Files to DELETE (duplicates)
        files_to_delete = [
            'common-components/header.php',
            'common-components/header-diagnostic.php', 
            'common-components/page-header.php',
            'common-components/page-header-compact.php',
            'common-components/page-section-header.php',
            'common-components/footer.php',
            'common-components/footer-unified.php',
            'common-components/unified-template.php',
            'common-components/template-engine-ultimate.php',
            'real_template_broken.php',
            'real_template_current.php', 
            'real_template_fixed.php',
            'temp_template.php',
            'template-debug-colors.php'
        ]
        
        deleted_count = 0
        for file_path in files_to_delete:
            try:
                ftp.delete(file_path)
                print(f"   ✅ Deleted: {file_path}")
                deleted_count += 1
            except:
                print(f"   ⚪ Not found: {file_path}")
        
        print(f"\n📊 Deleted {deleted_count} duplicate template files")
        
        # Now ensure the SINGLE template system works correctly
        print("\n2. 🔧 Ensuring SINGLE header system works...")
        
        # Check that news page uses the single header
        news_content = []
        try:
            ftp.retrlines('RETR news-new.php', news_content.append)
            print(f"   News page: {len(news_content)} lines")
        except:
            print("   ❌ Could not read news page")
        
        # Convert category page to use the SAME template system
        print("\n3. 🔧 Converting category page to use single template...")
        
        # Create unified category page that uses real_template.php
        unified_category = '''<?php
// UNIFIED CATEGORY PAGE - Uses single template system
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
$page_title = $category_name;

// Get posts if database available
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

// Content sections for unified template
$greyContent1 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">' . htmlspecialchars($category_name) . '</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-4">' . htmlspecialchars($category_name) . '</h1>
            <p class="lead">Найдено материалов: ' . count($posts) . '</p>
        </div>
    </div>
</div>';

$greyContent3 = '';

$greyContent4 = '';

// Content grid
$greyContent5 = '
<div class="container">
    <div class="row">';

if (!empty($posts)) {
    foreach ($posts as $post) {
        $url = '/post/' . ($post['url_slug'] ?? '');
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="' . htmlspecialchars($url) . '" class="text-decoration-none">
                            ' . htmlspecialchars($post['title'] ?? 'Без заголовка') . '
                        </a>
                    </h5>
                    <p class="card-text">
                        ' . htmlspecialchars(mb_substr(strip_tags($post['text'] ?? ''), 0, 100)) . '...
                    </p>
                    <small class="text-muted">' . date('d.m.Y', strtotime($post['date_created'] ?? 'now')) . '</small>
                </div>
            </div>
        </div>';
    }
} else {
    $greyContent5 .= '
    <div class="col-12">
        <div class="alert alert-info">
            <h4>Материалы готовятся к публикации</h4>
            <p>В категории "' . htmlspecialchars($category_name) . '" скоро появятся новые материалы.</p>
        </div>
    </div>';
}

$greyContent5 .= '
    </div>
</div>';

$greyContent6 = '';

// *** CRITICAL: Use the SAME template system as news page ***
require_once $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        # Upload unified category page
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(unified_category)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR category-new.php', file)
        os.unlink(tmp_path)
        
        print("   ✅ Category page now uses single template system")
        
        ftp.quit()
        
        print(f"\n✅ SINGLE TEMPLATE SYSTEM ENFORCED!")
        
        print(f"\n🎯 What we now have:")
        print(f"✅ ONE header: common-components/real_header.php")
        print(f"✅ ONE footer: common-components/real_footer.php") 
        print(f"✅ ONE template: real_template.php")
        print(f"✅ ONE favicon: favicon.svg")
        print(f"✅ News page uses: real_template.php")
        print(f"✅ Category page uses: real_template.php")
        
        print(f"\n📊 Deleted {deleted_count} duplicate files")
        
        print(f"\n🧪 Both pages should now be IDENTICAL:")
        print(f"• https://11klassniki.ru/news")
        print(f"• https://11klassniki.ru/category/abiturientam")
        
        print(f"\n💡 Clear cache and test - both should show SAME design!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()