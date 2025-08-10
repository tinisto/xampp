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
    print("🔧 FIXING ALL PAGES TO USE UNIFIED TEMPLATE SYSTEM")
    print("Converting ALL broken pages to use real_template.php")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Fix VPO all regions
        vpo_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'ВУЗы всех регионов России';
$vpo = [];

if ($connection) {
    try {
        $stmt = $connection->prepare("SELECT id, vpo_name, city, region FROM vpo ORDER BY vpo_name ASC LIMIT 50");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $vpo[] = $row;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log('VPO query error: ' . $e->getMessage());
    }
}

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
            <h1 class="display-4 mb-4">🎓 ВУЗы всех регионов России</h1>
            <p class="lead">Найдено высших учебных заведений: ' . count($vpo) . '</p>
        </div>
    </div>
</div>';

$greyContent3 = '';
$greyContent4 = '';

$greyContent5 = '
<div class="container">
    <div class="row">';

if (!empty($vpo)) {
    foreach ($vpo as $institution) {
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/vpo/' . htmlspecialchars($institution['id'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($institution['vpo_name'] ?? 'Без названия') . '
                        </a>
                    </h5>
                    <p class="card-text">
                        <i class="fas fa-map-marker-alt me-1"></i>' . htmlspecialchars($institution['city'] ?? '') . '
                        <br><small class="text-muted">' . htmlspecialchars($institution['region'] ?? '') . '</small>
                    </p>
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
        </div>
    </div>';
}

$greyContent5 .= '
    </div>
</div>';

$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, vpo_page, 'vpo-all-regions-real.php')
        print("   ✅ Fixed vpo-all-regions-real.php")
        
        # 2. Fix SPO all regions
        spo_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'СПО всех регионов России';
$spo = [];

if ($connection) {
    try {
        $stmt = $connection->prepare("SELECT id, spo_name, city, region FROM spo ORDER BY spo_name ASC LIMIT 50");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $spo[] = $row;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log('SPO query error: ' . $e->getMessage());
    }
}

$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">СПО</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-4">🏢 СПО всех регионов России</h1>
            <p class="lead">Найдено учреждений среднего профессионального образования: ' . count($spo) . '</p>
        </div>
    </div>
</div>';

$greyContent3 = '';
$greyContent4 = '';

$greyContent5 = '
<div class="container">
    <div class="row">';

if (!empty($spo)) {
    foreach ($spo as $institution) {
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/spo/' . htmlspecialchars($institution['id'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($institution['spo_name'] ?? 'Без названия') . '
                        </a>
                    </h5>
                    <p class="card-text">
                        <i class="fas fa-map-marker-alt me-1"></i>' . htmlspecialchars($institution['city'] ?? '') . '
                        <br><small class="text-muted">' . htmlspecialchars($institution['region'] ?? '') . '</small>
                    </p>
                </div>
            </div>
        </div>';
    }
} else {
    $greyContent5 .= '
    <div class="col-12">
        <div class="alert alert-info">
            <h4><i class="fas fa-building me-2"></i>СПО загружается</h4>
            <p>Информация об учреждениях среднего профессионального образования всех регионов России скоро появится на сайте.</p>
        </div>
    </div>';
}

$greyContent5 .= '
    </div>
</div>';

$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, spo_page, 'spo-all-regions-real.php')
        print("   ✅ Fixed spo-all-regions-real.php")
        
        # 3. Fix main index page
        index_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = '11klassniki.ru - Портал образования России';
$recent_news = [];

if ($connection) {
    try {
        $stmt = $connection->prepare("SELECT id, title_post as title, text_post as text, url_slug, date_post FROM posts ORDER BY date_post DESC LIMIT 6");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $recent_news[] = $row;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log('Index query error: ' . $e->getMessage());
    }
}

$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12 text-center">
            <h1 class="display-3 mb-4">🎓 Портал образования России</h1>
            <p class="lead">Информация о школах, ВУЗах, СПО и новости образования</p>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row text-center">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-school fa-3x text-primary mb-3"></i>
                    <h4>Школы</h4>
                    <p>Информация о школах всех регионов России</p>
                    <a href="/schools-all-regions" class="btn btn-primary">Перейти</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-university fa-3x text-success mb-3"></i>
                    <h4>ВУЗы</h4>
                    <p>Высшие учебные заведения России</p>
                    <a href="/vpo-all-regions" class="btn btn-success">Перейти</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-building fa-3x text-warning mb-3"></i>
                    <h4>СПО</h4>
                    <p>Среднее профессиональное образование</p>
                    <a href="/spo-all-regions" class="btn btn-warning">Перейти</a>
                </div>
            </div>
        </div>
    </div>
</div>';

$greyContent3 = '';
$greyContent4 = '';

$greyContent5 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">📰 Последние новости</h2>
        </div>
    </div>
    <div class="row">';

if (!empty($recent_news)) {
    foreach ($recent_news as $news) {
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/post/' . htmlspecialchars($news['url_slug'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($news['title'] ?? 'Без заголовка') . '
                        </a>
                    </h5>
                    <p class="card-text">
                        ' . htmlspecialchars(mb_substr(strip_tags($news['text'] ?? ''), 0, 100)) . '...
                    </p>
                    <small class="text-muted">' . date('d.m.Y', strtotime($news['date_post'] ?? 'now')) . '</small>
                </div>
            </div>
        </div>';
    }
} else {
    $greyContent5 .= '
    <div class="col-12">
        <div class="alert alert-info">
            <h4>Новости загружаются</h4>
            <p>Скоро здесь появятся последние новости образования.</p>
        </div>
    </div>';
}

$greyContent5 .= '
    </div>
</div>';

$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, index_page, 'index.php')
        print("   ✅ Fixed index.php")
        
        # 4. Fix search page
        search_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'Поиск по сайту';
$query = $_GET['q'] ?? '';
$results = [];

if (!empty($query) && $connection) {
    try {
        $search_term = '%' . $query . '%';
        $stmt = $connection->prepare("SELECT 'post' as type, id, title_post as title, text_post as text, url_slug FROM posts WHERE title_post LIKE ? OR text_post LIKE ? ORDER BY date_post DESC LIMIT 20");
        if ($stmt) {
            $stmt->bind_param("ss", $search_term, $search_term);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log('Search query error: ' . $e->getMessage());
    }
}

$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-4">🔍 Поиск по сайту</h1>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="q" class="form-control form-control-lg" placeholder="Введите поисковый запрос..." value="' . htmlspecialchars($query) . '">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="fas fa-search"></i> Найти
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>';

$greyContent3 = '';
$greyContent4 = '';

$greyContent5 = '';
if (!empty($query)) {
    $greyContent5 = '
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3>Результаты поиска для: "' . htmlspecialchars($query) . '"</h3>
                <p>Найдено результатов: ' . count($results) . '</p>
            </div>
        </div>
        <div class="row">';
        
    if (!empty($results)) {
        foreach ($results as $item) {
            $greyContent5 .= '
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="/post/' . htmlspecialchars($item['url_slug'] ?? '') . '" class="text-decoration-none">
                                ' . htmlspecialchars($item['title'] ?? 'Без заголовка') . '
                            </a>
                        </h5>
                        <p class="card-text">
                            ' . htmlspecialchars(mb_substr(strip_tags($item['text'] ?? ''), 0, 200)) . '...
                        </p>
                    </div>
                </div>
            </div>';
        }
    } else {
        $greyContent5 .= '
        <div class="col-12">
            <div class="alert alert-warning">
                <h4>Ничего не найдено</h4>
                <p>По вашему запросу ничего не найдено. Попробуйте изменить запрос.</p>
            </div>
        </div>';
    }
    
    $greyContent5 .= '
        </div>
    </div>';
}

$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, search_page, 'search-new.php')
        print("   ✅ Fixed search-new.php")
        
        ftp.quit()
        
        print(f"\n✅ ALL PAGES FIXED TO USE UNIFIED TEMPLATE!")
        
        print(f"\n🎯 Fixed pages:")
        print(f"✅ vpo-all-regions-real.php - Uses template")
        print(f"✅ spo-all-regions-real.php - Uses template")
        print(f"✅ index.php - Uses template")  
        print(f"✅ search-new.php - Uses template")
        print(f"✅ schools-all-regions-real.php - Already fixed")
        print(f"✅ news-new.php - Already fixed")
        print(f"✅ category-new.php - Already fixed")
        
        print(f"\n🧪 All pages now use same template system:")
        print(f"• https://11klassniki.ru/")
        print(f"• https://11klassniki.ru/news")
        print(f"• https://11klassniki.ru/category/abiturientam")
        print(f"• https://11klassniki.ru/schools-all-regions")
        print(f"• https://11klassniki.ru/vpo-all-regions")
        print(f"• https://11klassniki.ru/spo-all-regions")
        print(f"• https://11klassniki.ru/search")
        
        print(f"\n✅ ALL INTERNAL SERVER ERRORS SHOULD BE FIXED!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()