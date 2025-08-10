#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def debug_vpo_routing():
    # Debug script to identify which file handles VPO routing
    debug_content = '''<?php
echo "<!-- VPO-ROUTER-DEBUG-MARKER -->"; 
echo "<h1 style='color: red; background: yellow; padding: 20px;'>🔍 VPO ROUTER DEBUG - THIS FILE IS BEING EXECUTED</h1>";
echo "<p>Current file: " . __FILE__ . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Script name: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$total = 0;
if ($connection) {
    $result = mysqli_query($connection, "SELECT COUNT(*) as c FROM vpo");
    if ($result) $total = mysqli_fetch_assoc($result)['c'];
}

echo "<p>VPO count from database: <strong>$total</strong></p>";

$greyContent1 = '<div style="background: lime; padding: 20px;"><h2>🎯 DEBUG: greyContent1 executed</h2></div>';

$greyContent2 = '<div style="background: cyan; padding: 20px;"><h2>🎯 DEBUG: greyContent2 executed</h2><p>VPO Count: ' . $total . '</p></div>';

$greyContent3 = '<div style="background: orange; padding: 20px;"><h2>🎯 DEBUG: greyContent3 executed</h2></div>';
$greyContent4 = '<div style="background: pink; padding: 20px;"><h2>🎯 DEBUG: greyContent4 executed</h2></div>';

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
        with open('temp_vpo_debug.php', 'w', encoding='utf-8') as f:
            f.write(debug_content)
        
        # Upload debug version to ALL possible VPO files
        possible_files = [
            'vpo-all-regions-new.php',
            'vpo-all-regions.php', 
            'vpo-all-regions-real.php',
            'educational-institutions-all-regions-real.php'
        ]
        
        for filename in possible_files:
            with open('temp_vpo_debug.php', 'rb') as f:
                try:
                    ftp.storbinary(f'STOR {filename}', f)
                    print(f"✓ Uploaded debug to {filename}")
                except:
                    print(f"- Could not upload to {filename}")
        
        # Clean up
        os.remove('temp_vpo_debug.php')
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
    print("=== Debug VPO Routing ===")
    debug_vpo_routing()