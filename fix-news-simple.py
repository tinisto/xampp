#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def fix_news_simple():
    # Fixed version of news-simple.php
    fixed_content = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'Новости образования';

// Get current page
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 12;
$offset = ($current_page - 1) * $per_page;

// Initialize variables
$posts = [];
$total_news = 0;

// Get news using correct field names
if ($connection) {
    // Get total count
    $result = mysqli_query($connection, "SELECT COUNT(*) as c FROM news");
    if ($result) {
        $total_news = mysqli_fetch_assoc($result)['c'];
    }
    
    // Get posts with correct field names
    $query = "SELECT id, title_news, text_news, url_slug, date_news, category_news FROM news ORDER BY date_news DESC LIMIT $per_page OFFSET $offset";
    $result = mysqli_query($connection, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $posts[] = $row;
        }
    }
}

$total_pages = ceil($total_news / $per_page);

$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Новости</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-2">📰 Новости образования</h1>
            <p class="lead mb-4">Актуальные новости из мира образования, изменения в ЕГЭ, события в учебных заведениях</p>
            <p class="text-muted">Всего новостей: ' . number_format($total_news) . '</p>
        </div>
    </div>
</div>';

$greyContent3 = '';
$greyContent4 = '';

$greyContent5 = '
<div class="container">
    <div class="row g-4">';

if (!empty($posts)) {
    foreach ($posts as $post) {
        // Use correct field names from news table
        $title = $post['title_news'] ?? 'Без заголовка';
        $text = $post['text_news'] ?? '';
        $url = $post['url_slug'] ?? '';
        $date = $post['date_news'] ?? date('Y-m-d');
        
        // Map category numbers to titles
        $categoryTitle = 'Новости';
        switch ($post['category_news']) {
            case '1': $categoryTitle = 'Новости ВПО'; break;
            case '2': $categoryTitle = 'Новости СПО'; break;
            case '3': $categoryTitle = 'Новости школ'; break;
            case '4': $categoryTitle = 'Новости образования'; break;
        }
        
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <span class="badge bg-primary mb-2">' . htmlspecialchars($categoryTitle) . '</span>
                    <h5 class="card-title">
                        <a href="/news/' . htmlspecialchars($url) . '" class="text-decoration-none">
                            ' . htmlspecialchars($title) . '
                        </a>
                    </h5>
                    <p class="card-text">
                        ' . htmlspecialchars(mb_substr(strip_tags($text), 0, 150)) . '...
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="far fa-calendar me-1"></i>' . date('d.m.Y', strtotime($date)) . '
                        </small>
                        <a href="/news/' . htmlspecialchars($url) . '" class="btn btn-sm btn-outline-primary">
                            Читать далее
                        </a>
                    </div>
                </div>
            </div>
        </div>';
    }
} else {
    $greyContent5 .= '
    <div class="col-12">
        <div class="alert alert-info">
            <h4><i class="fas fa-newspaper me-2"></i>База данных новостей</h4>
            <p>В базе данных найдено: <strong>' . $total_news . '</strong> новостей.</p>
            ' . ($total_news > 0 ? '<p>Проблема с загрузкой новостей. Попробуйте обновить страницу.</p>' : '<p>Скоро здесь появятся свежие новости образования.</p>') . '
        </div>
    </div>';
}

$greyContent5 .= '
    </div>
</div>';

// Pagination
if ($total_pages > 1) {
    $greyContent5 .= '
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="Страницы новостей">
                    <ul class="pagination justify-content-center">';
    
    if ($current_page > 1) {
        $greyContent5 .= '<li class="page-item">
            <a class="page-link" href="/news?page=' . ($current_page - 1) . '">Предыдущая</a>
        </li>';
    }
    
    for ($i = 1; $i <= min($total_pages, 10); $i++) {
        $greyContent5 .= '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '">
            <a class="page-link" href="/news?page=' . $i . '">' . $i . '</a>
        </li>';
    }
    
    if ($current_page < $total_pages) {
        $greyContent5 .= '<li class="page-item">
            <a class="page-link" href="/news?page=' . ($current_page + 1) . '">Следующая</a>
        </li>';
    }
    
    $greyContent5 .= '
                    </ul>
                </nav>
            </div>
        </div>
    </div>';
}

$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''

    try:
        print("Connecting to FTP...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create temporary file
        with open('temp_news_simple_fixed.php', 'w', encoding='utf-8') as f:
            f.write(fixed_content)
        
        # Upload fixed version
        with open('temp_news_simple_fixed.php', 'rb') as f:
            ftp.storbinary('STOR news-simple.php', f)
        
        print("✓ Updated news-simple.php with correct database field names")
        
        # Clean up
        os.remove('temp_news_simple_fixed.php')
        ftp.quit()
        
        print("\\n🎉 Fix deployed!")
        print("\\nChanges made:")
        print("- Fixed database query: date_post → date_news") 
        print("- Fixed field names: title → title_news, content → text_news")
        print("- Added category badges")
        print("- Fixed news URLs to use /news/ instead of /post/")
        print("\\nTest: https://11klassniki.ru/news")
        
    except Exception as e:
        print(f"✗ Error: {e}")

if __name__ == "__main__":
    print("=== Fixing News Simple Page ===")
    fix_news_simple()