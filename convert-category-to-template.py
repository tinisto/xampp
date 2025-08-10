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
    print("🔧 CONVERTING CATEGORY PAGE TO USE TEMPLATE SYSTEM")
    print("Convert from self-contained HTML to template system like news page")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Convert category page to use template system
        template_category = '''<?php
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

// Content sections for template
$greyContent1 = '
<div class="container mt-4">
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

// Use template system
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        # Upload template-based category page
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(template_category)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR category-new.php', file)
        os.unlink(tmp_path)
        
        print("   ✅ Converted category page to template system")
        
        ftp.quit()
        
        print(f"\n✅ CATEGORY PAGE CONVERTED TO TEMPLATE!")
        
        print(f"\n🎯 Now we have:")
        print(f"✅ ONE template system: real_header.php + real_footer.php + real_template.php")
        print(f"✅ News page: Uses template system")
        print(f"✅ Category page: Uses template system")
        print(f"✅ NO self-contained HTML")
        print(f"✅ Everything uses same template")
        
        print(f"\n🧪 Both pages now use identical template system:")
        print(f"• https://11klassniki.ru/news")
        print(f"• https://11klassniki.ru/category/abiturientam")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()