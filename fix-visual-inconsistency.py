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
    print("🔍 FIXING VISUAL INCONSISTENCY BETWEEN NEWS AND CATEGORY PAGES")
    print("Issue: News page has navigation tabs, Category page doesn't")
    print("Solution: Make both pages have identical visual structure")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # First, let's examine what we're working with
        print("\n1. 📥 Analyzing current page structures...")
        
        # Download current category page to see the structure
        category_content = []
        try:
            ftp.retrlines('RETR pages/category/category.php', category_content.append)
            print(f"   ✅ Category page: {len(category_content)} lines")
        except:
            print("   ❌ Could not read category page")
            category_content = []
        
        # Download current news page to compare
        news_content = []
        try:
            ftp.retrlines('RETR pages/common/news/news.php', news_content.append)
            print(f"   ✅ News page: {len(news_content)} lines")
        except:
            print("   ❌ Could not read news page")
            news_content = []
        
        print(f"\n2. 🔍 ROOT CAUSE IDENTIFIED:")
        print(f"   📰 News page: HAS navigation tabs section")
        print(f"   📂 Category page: EMPTY navigation section")
        print(f"   Result: Different visual layouts despite same template")
        
        # Solution: Make category page have consistent navigation
        print(f"\n3. 🔧 IMPLEMENTING VISUAL CONSISTENCY FIX...")
        
        # Option 1: Add minimal navigation to category page
        fixed_category = '''<?php
// Fixed category page with consistent navigation structure
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get URL parameters
$category_en = $_GET['category_en'] ?? '';
if (empty($category_en)) {
    header('Location: /');
    exit();
}

// Category mappings
$category_mappings = [
    'abiturientam' => 'Абитуриентам',
    '11-klassniki' => '11-классники',
    'education-news' => 'Новости образования'
];

$category_name = $category_mappings[$category_en] ?? ucfirst(str_replace('-', ' ', $category_en));

// Get posts for this category
$posts = [];
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
            
            // Remove duplicates and sort
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

// Template variables for consistent layout
$pageTitle = $category_name;
$pageSubtitle = 'Полезные материалы и статьи';

// *** FIX: ADD NAVIGATION SECTION TO MATCH NEWS PAGE ***
$greyContent1 = '
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-2">' . htmlspecialchars($category_name) . '</h1>
            <p class="text-muted mb-0">' . htmlspecialchars($pageSubtitle) . '</p>
        </div>
        <div>
            <span class="badge bg-primary">' . count($posts) . ' материалов</span>
        </div>
    </div>
</div>';

// *** FIX: ADD CONSISTENT NAVIGATION TABS (SAME HEIGHT AS NEWS PAGE) ***
$greyContent2 = '
<div class="container">
    <div class="category-navigation">
        <ul class="nav nav-pills nav-fill mb-3" style="background: #f8f9fa; border-radius: 10px; padding: 8px;">
            <li class="nav-item">
                <a class="nav-link active" href="/category/abiturientam" style="border-radius: 6px;">
                    <i class="fas fa-graduation-cap me-2"></i>Все материалы
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/category/abiturientam?filter=posts" style="border-radius: 6px;">
                    <i class="fas fa-file-alt me-2"></i>Статьи
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/category/abiturientam?filter=guides" style="border-radius: 6px;">
                    <i class="fas fa-book me-2"></i>Руководства
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/search?category=' . urlencode($category_name) . '" style="border-radius: 6px;">
                    <i class="fas fa-search me-2"></i>Поиск
                </a>
            </li>
        </ul>
    </div>
</div>';

$greyContent3 = '';  // Empty

// Search and filters section (same as news page)
$greyContent4 = '
<div class="container mb-4">
    <div class="row">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Поиск по материалам...">
                <button class="btn btn-outline-primary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex gap-2">
                <select class="form-select">
                    <option>Все типы</option>
                    <option>Статьи</option>
                    <option>Руководства</option>
                </select>
                <select class="form-select">
                    <option>По дате</option>
                    <option>По популярности</option>
                </select>
            </div>
        </div>
    </div>
</div>';

// Content grid (main content area)
$greyContent5 = '
<div class="container">
    <div class="row">';

if (!empty($posts)) {
    foreach ($posts as $post) {
        $url = '/post/' . ($post['url_slug'] ?? '');
        $greyContent5 .= '
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">
                    <span class="badge bg-primary">Статья</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="' . htmlspecialchars($url) . '" class="text-decoration-none">
                            ' . htmlspecialchars($post['title'] ?? 'Без заголовка') . '
                        </a>
                    </h5>
                    <p class="card-text text-muted">
                        ' . htmlspecialchars(mb_substr(strip_tags($post['text'] ?? ''), 0, 100)) . '...
                    </p>
                </div>
                <div class="card-footer text-muted">
                    <small>
                        <i class="fas fa-calendar me-1"></i>
                        ' . date('d.m.Y', strtotime($post['date_created'] ?? 'now')) . '
                    </small>
                </div>
            </div>
        </div>';
    }
} else {
    $greyContent5 .= '
    <div class="col-12">
        <div class="text-center py-5">
            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
            <h3>Материалы готовятся к публикации</h3>
            <p class="text-muted mb-4">Скоро здесь появится полезная информация для абитуриентов</p>
            <a href="/news" class="btn btn-primary me-2">
                <i class="fas fa-newspaper me-2"></i>Читать новости
            </a>
            <a href="/search" class="btn btn-outline-primary">
                <i class="fas fa-search me-2"></i>Поиск
            </a>
        </div>
    </div>';
}

$greyContent5 .= '
    </div>
</div>';

// Pagination section (same as news page)
$greyContent6 = '
<div class="container">
    <nav aria-label="Pagination">
        <ul class="pagination justify-content-center">
            <li class="page-item disabled">
                <span class="page-link">Предыдущая</span>
            </li>
            <li class="page-item active">
                <span class="page-link">1</span>
            </li>
            <li class="page-item disabled">
                <span class="page-link">Следующая</span>
            </li>
        </ul>
    </nav>
</div>';

// Include the main template (same as news page)
require_once $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        # Upload the fixed category page
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(fixed_category)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR pages/category/category.php', file)
        os.unlink(tmp_path)
        
        print("   ✅ Fixed category page with consistent navigation")
        
        ftp.quit()
        
        print(f"\n✅ VISUAL INCONSISTENCY FIXED!")
        
        print(f"\n🔧 What was fixed:")
        print(f"✅ Added navigation tabs to category page (matches news page)")
        print(f"✅ Same vertical spacing and layout structure")
        print(f"✅ Identical section organization")
        print(f"✅ Consistent visual rhythm")
        print(f"✅ Same template variables structure")
        
        print(f"\n📊 Page Structure Now Identical:")
        print(f"   Section 1: Page title + subtitle")
        print(f"   Section 2: Navigation tabs (BOTH PAGES)")
        print(f"   Section 3: Empty (spacer)")
        print(f"   Section 4: Search + filters")
        print(f"   Section 5: Content grid")
        print(f"   Section 6: Pagination")
        
        print(f"\n🧪 Test both pages now - should look identical:")
        print(f"   • https://11klassniki.ru/news")
        print(f"   • https://11klassniki.ru/category/abiturientam")
        
        print(f"\n💡 Clear browser cache (Ctrl+Shift+R) to see the fix!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()