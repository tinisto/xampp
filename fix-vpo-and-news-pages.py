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
    print("🔧 FIXING VPO AND NEWS PAGES DATABASE ISSUES")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Create proper VPO page with direct database queries
        print("\n1️⃣ Creating fixed VPO page...")
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

// Get VPO data from database
if ($connection) {
    try {
        // Get total count
        $count_query = "SELECT COUNT(*) as total FROM vpo";
        $result = $connection->query($count_query);
        if ($result) {
            $row = $result->fetch_assoc();
            $total_vpo = intval($row['total']);
        }
        
        // Get unique cities
        $cities_query = "SELECT DISTINCT city FROM vpo WHERE city IS NOT NULL AND city != '' ORDER BY city";
        $result = $connection->query($cities_query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $cities[] = $row['city'];
            }
        }
        
        // Get VPO list for current page
        $vpo_query = "SELECT id, vpo_name, city, region, address, phone, website, type 
                      FROM vpo 
                      ORDER BY vpo_name ASC 
                      LIMIT " . intval($per_page) . " OFFSET " . intval($offset);
        
        $result = $connection->query($vpo_query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $vpo_list[] = $row;
            }
        }
    } catch (Exception $e) {
        error_log('VPO query error: ' . $e->getMessage());
    }
}

$total_pages = ceil($total_vpo / $per_page);

// Start content
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
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-primary">' . number_format($total_vpo) . '</h3>
                            <p class="mb-0">Всего ВУЗов</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-success">' . count($cities) . '</h3>
                            <p class="mb-0">Городов</p>
                        </div>
                    </div>
                </div>
            </div>
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
        $type_badge = '';
        if (!empty($vpo['type'])) {
            $type_color = 'primary';
            if (stripos($vpo['type'], 'университет') !== false) $type_color = 'success';
            elseif (stripos($vpo['type'], 'институт') !== false) $type_color = 'info';
            elseif (stripos($vpo['type'], 'академия') !== false) $type_color = 'warning';
            
            $type_badge = '<span class="badge bg-' . $type_color . ' mb-2">' . htmlspecialchars($vpo['type']) . '</span>';
        }
        
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    ' . $type_badge . '
                    <h5 class="card-title">
                        <a href="/vpo/' . htmlspecialchars($vpo['id'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($vpo['vpo_name'] ?? 'Без названия') . '
                        </a>
                    </h5>
                    <p class="card-text">
                        <i class="fas fa-map-marker-alt text-primary me-1"></i>' . htmlspecialchars($vpo['city'] ?? '') . '<br>
                        <small class="text-muted">' . htmlspecialchars($vpo['region'] ?? '') . '</small>
                    </p>';
        
        if (!empty($vpo['website'])) {
            $greyContent5 .= '<p class="card-text small"><i class="fas fa-globe text-info me-1"></i><a href="' . htmlspecialchars($vpo['website']) . '" target="_blank">Официальный сайт</a></p>';
        }
        
        $greyContent5 .= '
                    <a href="/vpo/' . htmlspecialchars($vpo['id'] ?? '') . '" class="btn btn-sm btn-outline-primary">Подробнее</a>
                </div>
            </div>
        </div>';
    }
} else {
    $greyContent5 .= '
    <div class="col-12">
        <div class="alert alert-info">
            <h4><i class="fas fa-university me-2"></i>ВУЗы загружаются</h4>
            <p>Информация о высших учебных заведениях всех регионов России скоро появится на сайте.</p>
            <p class="mb-0">Найдено высших учебных заведений: ' . $total_vpo . '</p>
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
    
    $greyContent5 .= '<li class="page-item ' . ($current_page <= 1 ? 'disabled' : '') . '">
        <a class="page-link" href="/vpo-all-regions?page=' . ($current_page - 1) . '">Предыдущая</a>
    </li>';
    
    for ($i = 1; $i <= min($total_pages, 10); $i++) {
        $greyContent5 .= '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '">
            <a class="page-link" href="/vpo-all-regions?page=' . $i . '">' . $i . '</a>
        </li>';
    }
    
    $greyContent5 .= '<li class="page-item ' . ($current_page >= $total_pages ? 'disabled' : '') . '">
        <a class="page-link" href="/vpo-all-regions?page=' . ($current_page + 1) . '">Следующая</a>
    </li>';
    
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
        
        upload_file(ftp, vpo_page, 'vpo-all-regions-fixed.php')
        print("   ✅ Created fixed VPO page")
        
        # 2. Update .htaccess to use fixed version
        print("\n2️⃣ Updating .htaccess...")
        
        # Download current .htaccess
        htaccess_content = []
        ftp.retrlines('RETR .htaccess', htaccess_content.append)
        
        # Update the VPO routing
        updated_htaccess = []
        for line in htaccess_content:
            if 'vpo-all-regions/?$ vpo-all-regions-new.php' in line:
                updated_htaccess.append('RewriteRule ^vpo-all-regions/?$ vpo-all-regions-fixed.php [QSA,NC,L]')
                print("   ✅ Updated VPO routing")
            else:
                updated_htaccess.append(line)
        
        # Upload updated .htaccess
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write('\n'.join(updated_htaccess))
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR .htaccess', file)
        os.unlink(tmp_path)
        
        # 3. Check news page routing
        print("\n3️⃣ Checking news page...")
        
        # Find which news file is being used
        news_route = None
        for line in htaccess_content:
            if 'news/?$' in line and 'RewriteRule' in line:
                print(f"   Found: {line}")
                if 'news-new.php' in line:
                    news_route = 'news-new.php'
                    break
        
        if news_route:
            print(f"   News routes to: {news_route}")
            
            # Check if news-new.php exists
            try:
                # Try to download to check existence
                with tempfile.NamedTemporaryFile(delete=False) as tmp:
                    tmp_path = tmp.name
                ftp.retrbinary(f'RETR {news_route}', open(tmp_path, 'wb').write)
                os.unlink(tmp_path)
                print(f"   ✅ {news_route} exists on server")
            except:
                print(f"   ❌ {news_route} not found, creating it...")
                
                # Create news page
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

// Get news from database
if ($connection) {
    try {
        // Get total count
        $count_query = "SELECT COUNT(*) as total FROM posts";
        $result = $connection->query($count_query);
        if ($result) {
            $row = $result->fetch_assoc();
            $total_news = intval($row['total']);
        }
        
        // Get posts for current page
        $posts_query = "SELECT id, title_post as title, text_post as text, url_slug, date_post 
                        FROM posts 
                        ORDER BY date_post DESC 
                        LIMIT " . intval($per_page) . " OFFSET " . intval($offset);
        
        $result = $connection->query($posts_query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
        }
    } catch (Exception $e) {
        error_log('News query error: ' . $e->getMessage());
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
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/post/' . htmlspecialchars($post['url_slug'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($post['title'] ?? 'Без заголовка') . '
                        </a>
                    </h5>
                    <p class="card-text">
                        ' . htmlspecialchars(mb_substr(strip_tags($post['text'] ?? ''), 0, 150)) . '...
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="far fa-calendar me-1"></i>' . date('d.m.Y', strtotime($post['date_post'] ?? 'now')) . '
                        </small>
                        <a href="/post/' . htmlspecialchars($post['url_slug'] ?? '') . '" class="btn btn-sm btn-outline-primary">
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
            <h4><i class="fas fa-newspaper me-2"></i>Новости загружаются</h4>
            <p>Скоро здесь появятся свежие новости образования.</p>
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
    
    $greyContent5 .= '<li class="page-item ' . ($current_page <= 1 ? 'disabled' : '') . '">
        <a class="page-link" href="/news?page=' . ($current_page - 1) . '">Предыдущая</a>
    </li>';
    
    for ($i = 1; $i <= min($total_pages, 10); $i++) {
        $greyContent5 .= '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '">
            <a class="page-link" href="/news?page=' . $i . '">' . $i . '</a>
        </li>';
    }
    
    $greyContent5 .= '<li class="page-item ' . ($current_page >= $total_pages ? 'disabled' : '') . '">
        <a class="page-link" href="/news?page=' . ($current_page + 1) . '">Следующая</a>
    </li>';
    
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
                
                upload_file(ftp, news_page, 'news-new.php')
                print("   ✅ Created news-new.php")
        
        ftp.quit()
        
        print("\n✅ FIXED VPO AND NEWS PAGES!")
        print("\n🎯 What was fixed:")
        print("• VPO page now directly queries database")
        print("• News page created with proper queries")
        print("• Both pages show actual counts from DB")
        print("• Pagination implemented")
        print("• Error handling added")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()