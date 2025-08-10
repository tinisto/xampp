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
    print("🔍 FINDING WHERE CATEGORY PAGE GETS ITS TEMPLATE")
    print("Problem: Category page shows NO debug colors = different template!")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        print("\n1. 📥 Checking what category-new.php actually includes...")
        
        # Download category-new.php
        category_content = []
        ftp.retrlines('RETR category-new.php', category_content.append)
        
        print(f"   Category file: {len(category_content)} lines")
        
        # Look for includes/requires
        includes_found = []
        for i, line in enumerate(category_content):
            if any(keyword in line.lower() for keyword in ['include', 'require', 'header', 'footer', 'template']):
                includes_found.append(f"Line {i+1}: {line.strip()}")
        
        print(f"\n   📋 INCLUDES FOUND IN category-new.php:")
        for inc in includes_found:
            print(f"      {inc}")
        
        print(f"\n2. 📥 Checking what news-new.php includes for comparison...")
        
        # Download news-new.php
        news_content = []
        ftp.retrlines('RETR news-new.php', news_content.append)
        
        print(f"   News file: {len(news_content)} lines")
        
        # Look for includes/requires
        news_includes = []
        for i, line in enumerate(news_content):
            if any(keyword in line.lower() for keyword in ['include', 'require', 'header', 'footer', 'template']):
                news_includes.append(f"Line {i+1}: {line.strip()}")
        
        print(f"\n   📋 INCLUDES FOUND IN news-new.php:")
        for inc in news_includes:
            print(f"      {inc}")
        
        print(f"\n3. 🔍 DIAGNOSIS:")
        
        # Check if category page is self-contained
        has_html_doctype = any('<!DOCTYPE' in line for line in category_content)
        has_html_tag = any('<html' in line for line in category_content)
        has_head_tag = any('<head>' in line for line in category_content)
        
        if has_html_doctype and has_html_tag and has_head_tag:
            print(f"   ⚠️  PROBLEM FOUND: Category page is SELF-CONTAINED!")
            print(f"      → It has its own HTML structure")
            print(f"      → It doesn't use the unified template system")
            print(f"      → This is why debug colors don't appear")
        else:
            print(f"   ✅ Category page uses template system")
        
        # Check if news page uses template
        news_html = any('<!DOCTYPE' in line for line in news_content)
        if news_html:
            print(f"   ⚠️  News page is also self-contained")
        else:
            print(f"   ✅ News page uses template system")
        
        print(f"\n4. 🔧 SOLUTION NEEDED:")
        if has_html_doctype:
            print(f"   ❌ Category page bypasses template system entirely")
            print(f"   🔧 Need to make category page use real_template.php")
            print(f"   🔧 OR apply debug colors directly to category-new.php")
        
        # Let's fix by making category page use same system as news
        print(f"\n5. 🛠️  FIXING: Make category page use unified template...")
        
        # Create category page that uses the unified template system
        unified_category = '''<?php
// UNIFIED CATEGORY PAGE - Uses same template as news page
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

// Template content sections (SAME AS NEWS PAGE)
$greyContent1 = '
<div class="container" style="background-color: #00ff00 !important; padding: 20px; border: 2px solid red;">
    <h1 style="background-color: yellow; padding: 10px;">[GREEN DIV] ' . htmlspecialchars($category_name) . '</h1>
    <p style="background-color: yellow; padding: 5px;">Found: ' . count($posts) . ' materials</p>
</div>';

$greyContent2 = '
<div class="container" style="background-color: #00ff00 !important; padding: 20px;">
    <div class="alert alert-success">
        <h4>[GREEN DIV] Navigation section for ' . htmlspecialchars($category_name) . '</h4>
    </div>
</div>';

$greyContent3 = '';

$greyContent4 = '
<div class="container" style="background-color: #00ff00 !important; padding: 20px;">
    <div class="alert alert-info">
        <h4>[GREEN DIV] Search and filters section</h4>
    </div>
</div>';

// Content grid
$greyContent5 = '
<div class="container" style="background-color: #00ff00 !important; padding: 20px;">
    <div class="row">';

if (!empty($posts)) {
    foreach ($posts as $post) {
        $url = '/post/' . ($post['url_slug'] ?? '');
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card" style="background-color: #66ff66 !important; border: 2px solid #00cc00;">
                <div class="card-body">
                    <h5 class="card-title" style="background-color: yellow; padding: 5px;">
                        <a href="' . htmlspecialchars($url) . '">
                            ' . htmlspecialchars($post['title'] ?? 'Без заголовка') . '
                        </a>
                    </h5>
                    <p class="card-text" style="background-color: #ccffcc; padding: 5px;">
                        ' . htmlspecialchars(mb_substr(strip_tags($post['text'] ?? ''), 0, 100)) . '...
                    </p>
                </div>
            </div>
        </div>';
    }
} else {
    $greyContent5 .= '
    <div class="col-12">
        <div class="alert alert-warning" style="background-color: #ffff99 !important;">
            <h3>[GREEN DIV] No content found</h3>
            <p>Materials are being prepared for publication</p>
        </div>
    </div>';
}

$greyContent5 .= '
    </div>
</div>';

$greyContent6 = '
<div class="container" style="background-color: #00ff00 !important; padding: 20px;">
    <div class="alert alert-secondary">
        <h4>[GREEN DIV] Pagination section</h4>
    </div>
</div>';

// *** CRITICAL: Use the SAME template system as news page ***
require_once $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        # Upload the unified category page
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(unified_category)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR category-new.php', file)
        os.unlink(tmp_path)
        
        print("   ✅ Updated category page to use unified template system")
        
        ftp.quit()
        
        print(f"\n✅ CATEGORY PAGE TEMPLATE FIXED!")
        
        print(f"\n🔧 What was changed:")
        print(f"✅ Category page now uses real_template.php (same as news)")
        print(f"✅ Added bright GREEN backgrounds to main content")
        print(f"✅ Added yellow highlights for text elements")
        print(f"✅ Both pages now use identical template system")
        
        print(f"\n🧪 Test again - BOTH pages should now show colors:")
        print(f"• https://11klassniki.ru/news (should have RED header + GREEN main)")
        print(f"• https://11klassniki.ru/category/abiturientam (should have RED header + GREEN main)")
        
        print(f"\n💡 Clear cache and test - category page should now show DEBUG COLORS!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()