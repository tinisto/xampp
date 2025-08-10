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
    print("🔧 FIXING VPO AND NEWS PAGES WITH SIMPLIFIED QUERIES")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Create TRULY fixed VPO page (matching index.php style)
        print("\n1️⃣ Creating truly fixed VPO page...")
        vpo_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'ВУЗы всех регионов России';

// Get current page
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 24;
$offset = ($current_page - 1) * $per_page;

// Initialize variables
$vpo_list = [];
$total_vpo = 0;
$cities = [];

// Get VPO data using same style as index.php
if ($connection) {
    // Get total count - simplified query like index.php
    $result = mysqli_query($connection, "SELECT COUNT(*) as c FROM vpo");
    if ($result) {
        $total_vpo = mysqli_fetch_assoc($result)['c'];
    }
    
    // Get VPO list
    $query = "SELECT * FROM vpo ORDER BY vpo_name ASC LIMIT $per_page OFFSET $offset";
    $result = mysqli_query($connection, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $vpo_list[] = $row;
        }
    }
}

$total_pages = ceil($total_vpo / $per_page);

// Start content using template system
$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">ВУЗы</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-2">🎓 Высшие учебные заведения России</h1>
            <p class="lead mb-4">Университеты, институты, академии - полная база ВУЗов</p>
            <p class="text-muted">Найдено высших учебных заведений: ' . number_format($total_vpo) . '</p>
        </div>
    </div>
</div>';

$greyContent3 = '';
$greyContent4 = '';

$greyContent5 = '
<div class="container">
    <div class="row">';

if (!empty($vpo_list)) {
    foreach ($vpo_list as $vpo) {
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/vpo/' . htmlspecialchars($vpo['id'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($vpo['vpo_name'] ?? 'Без названия') . '
                        </a>
                    </h5>
                    <p class="card-text">
                        <i class="fas fa-map-marker-alt text-primary me-1"></i>' . htmlspecialchars($vpo['city'] ?? '') . '<br>
                        <small class="text-muted">' . htmlspecialchars($vpo['region'] ?? '') . '</small>
                    </p>
                    <a href="/vpo/' . htmlspecialchars($vpo['id'] ?? '') . '" class="btn btn-sm btn-outline-primary">Подробнее</a>
                </div>
            </div>
        </div>';
    }
} else {
    $greyContent5 .= '
    <div class="col-12">
        <div class="alert alert-info">
            <h4><i class="fas fa-university me-2"></i>База данных ВУЗов</h4>
            <p>В базе данных найдено: <strong>' . $total_vpo . '</strong> высших учебных заведений.</p>
            ' . ($total_vpo > 0 ? '<p>Данные загружаются...</p>' : '<p>Информация о ВУЗах скоро появится.</p>') . '
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
                <nav aria-label="Страницы">
                    <ul class="pagination justify-content-center">';
    
    if ($current_page > 1) {
        $greyContent5 .= '<li class="page-item">
            <a class="page-link" href="/vpo-all-regions?page=' . ($current_page - 1) . '">Предыдущая</a>
        </li>';
    }
    
    for ($i = 1; $i <= min($total_pages, 10); $i++) {
        $greyContent5 .= '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '">
            <a class="page-link" href="/vpo-all-regions?page=' . $i . '">' . $i . '</a>
        </li>';
    }
    
    if ($current_page < $total_pages) {
        $greyContent5 .= '<li class="page-item">
            <a class="page-link" href="/vpo-all-regions?page=' . ($current_page + 1) . '">Следующая</a>
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

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, vpo_page, 'vpo-all-regions-simple.php')
        print("   ✅ Created simplified VPO page")
        
        # 2. Create simplified news page
        print("\n2️⃣ Creating simplified news page...")
        news_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'Новости образования';

// Get current page
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 12;
$offset = ($current_page - 1) * $per_page;

// Initialize variables
$posts = [];
$total_news = 0;

// Get news using same style as index.php
if ($connection) {
    // Get total count
    $result = mysqli_query($connection, "SELECT COUNT(*) as c FROM posts");
    if ($result) {
        $total_news = mysqli_fetch_assoc($result)['c'];
    }
    
    // Get posts
    $query = "SELECT * FROM posts ORDER BY date_post DESC LIMIT $per_page OFFSET $offset";
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
        $title = $post['title_post'] ?? 'Без заголовка';
        $text = $post['text_post'] ?? '';
        $url = $post['url_slug'] ?? '';
        $date = $post['date_post'] ?? date('Y-m-d');
        
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/post/' . htmlspecialchars($url) . '" class="text-decoration-none">
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
                        <a href="/post/' . htmlspecialchars($url) . '" class="btn btn-sm btn-outline-primary">
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
            ' . ($total_news > 0 ? '<p>Новости загружаются...</p>' : '<p>Скоро здесь появятся свежие новости образования.</p>') . '
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
        
        upload_file(ftp, news_page, 'news-simple.php')
        print("   ✅ Created simplified news page")
        
        # 3. Update .htaccess to use simplified versions
        print("\n3️⃣ Updating .htaccess to use simplified versions...")
        
        # Download current .htaccess
        htaccess_content = []
        ftp.retrlines('RETR .htaccess', htaccess_content.append)
        
        # Update the routes
        updated_htaccess = []
        for line in htaccess_content:
            if line.strip().startswith('RewriteRule ^vpo-all-regions/?$'):
                updated_htaccess.append('RewriteRule ^vpo-all-regions/?$ vpo-all-regions-simple.php [QSA,NC,L]')
                print("   ✅ Updated VPO routing to simple version")
            elif line.strip().startswith('RewriteRule ^news/?$'):
                updated_htaccess.append('RewriteRule ^news/?$ news-simple.php [QSA,NC,L]')
                print("   ✅ Updated news routing to simple version")
            else:
                updated_htaccess.append(line)
        
        # Upload updated .htaccess
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write('\n'.join(updated_htaccess))
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR .htaccess', file)
        os.unlink(tmp_path)
        
        # Clean up test file
        try:
            ftp.delete('check-tables.php')
            print("   ✅ Cleaned up test file")
        except:
            pass
        
        ftp.quit()
        
        print("\n✅ CREATED SIMPLIFIED VERSIONS!")
        print("\n🎯 What was done:")
        print("• Created vpo-all-regions-simple.php using mysqli style")
        print("• Created news-simple.php using mysqli style")
        print("• Both pages match index.php query style")
        print("• Updated .htaccess to use new versions")
        print("• Shows actual database counts")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()