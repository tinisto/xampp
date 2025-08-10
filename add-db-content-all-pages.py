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
    print("📊 ADDING DATABASE CONTENT TO ALL PAGES")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Update Schools page with DB content
        print("\n1️⃣ Updating Schools page...")
        schools_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'Школы всех регионов России';
$schools = [];
$regions = [];

// Get current page
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 30;
$offset = ($current_page - 1) * $per_page;
$total_schools = 0;

// Get schools from database
if ($connection) {
    try {
        // Get total count
        $result = $connection->query("SELECT COUNT(*) as total FROM schools");
        $total_schools = $result ? $result->fetch_assoc()['total'] : 0;
        
        // Get unique regions
        $result = $connection->query("SELECT DISTINCT region FROM schools WHERE region IS NOT NULL ORDER BY region");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $regions[] = $row['region'];
            }
        }
        
        // Get schools for current page
        $stmt = $connection->prepare("SELECT id, school_name, city, region, address, phone, website FROM schools ORDER BY region, city, school_name LIMIT ? OFFSET ?");
        if ($stmt) {
            $stmt->bind_param("ii", $per_page, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $schools[] = $row;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log('Schools query error: ' . $e->getMessage());
    }
}

$total_pages = ceil($total_schools / $per_page);

$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Школы</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-2">🏫 Школы всех регионов России</h1>
            <p class="lead mb-4">Полная база данных общеобразовательных учреждений</p>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-primary">' . number_format($total_schools) . '</h3>
                            <p class="mb-0">Всего школ</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-success">' . count($regions) . '</h3>
                            <p class="mb-0">Регионов</p>
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

if (!empty($schools)) {
    foreach ($schools as $school) {
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/school/' . htmlspecialchars($school['id'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($school['school_name'] ?? 'Без названия') . '
                        </a>
                    </h5>
                    <p class="card-text">
                        <i class="fas fa-map-marker-alt text-primary me-1"></i>' . htmlspecialchars($school['city'] ?? '') . '<br>
                        <small class="text-muted">' . htmlspecialchars($school['region'] ?? '') . '</small>
                    </p>';
        
        if (!empty($school['address'])) {
            $greyContent5 .= '<p class="card-text small"><i class="fas fa-home text-secondary me-1"></i>' . htmlspecialchars($school['address']) . '</p>';
        }
        
        if (!empty($school['phone'])) {
            $greyContent5 .= '<p class="card-text small"><i class="fas fa-phone text-success me-1"></i>' . htmlspecialchars($school['phone']) . '</p>';
        }
        
        $greyContent5 .= '
                    <a href="/school/' . htmlspecialchars($school['id'] ?? '') . '" class="btn btn-sm btn-outline-primary">Подробнее</a>
                </div>
            </div>
        </div>';
    }
} else {
    $greyContent5 .= '
    <div class="col-12">
        <div class="alert alert-info">
            <h4><i class="fas fa-school me-2"></i>Школы загружаются</h4>
            <p>Информация о школах всех регионов России скоро появится на сайте.</p>
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
        <a class="page-link" href="/schools-all-regions?page=' . ($current_page - 1) . '">Предыдущая</a>
    </li>';
    
    for ($i = 1; $i <= min($total_pages, 10); $i++) {
        $greyContent5 .= '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '">
            <a class="page-link" href="/schools-all-regions?page=' . $i . '">' . $i . '</a>
        </li>';
    }
    
    $greyContent5 .= '<li class="page-item ' . ($current_page >= $total_pages ? 'disabled' : '') . '">
        <a class="page-link" href="/schools-all-regions?page=' . ($current_page + 1) . '">Следующая</a>
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
        
        upload_file(ftp, schools_page, 'schools-all-regions-real.php')
        print("   ✅ Schools page updated with DB content")
        
        # 2. Update VPO page with more content
        print("\n2️⃣ Updating VPO page...")
        vpo_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'ВУЗы всех регионов России';
$vpo = [];
$cities = [];

// Get current page
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 24;
$offset = ($current_page - 1) * $per_page;
$total_vpo = 0;

if ($connection) {
    try {
        // Get total count
        $result = $connection->query("SELECT COUNT(*) as total FROM vpo");
        $total_vpo = $result ? $result->fetch_assoc()['total'] : 0;
        
        // Get unique cities
        $result = $connection->query("SELECT DISTINCT city FROM vpo WHERE city IS NOT NULL ORDER BY city");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $cities[] = $row['city'];
            }
        }
        
        // Get VPOs for current page
        $stmt = $connection->prepare("SELECT id, vpo_name, city, region, address, phone, website, type FROM vpo ORDER BY vpo_name ASC LIMIT ? OFFSET ?");
        if ($stmt) {
            $stmt->bind_param("ii", $per_page, $offset);
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

$total_pages = ceil($total_vpo / $per_page);

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

if (!empty($vpo)) {
    foreach ($vpo as $institution) {
        $type_badge = '';
        if (!empty($institution['type'])) {
            $type_color = 'primary';
            if (stripos($institution['type'], 'университет') !== false) $type_color = 'success';
            elseif (stripos($institution['type'], 'институт') !== false) $type_color = 'info';
            elseif (stripos($institution['type'], 'академия') !== false) $type_color = 'warning';
            
            $type_badge = '<span class="badge bg-' . $type_color . ' mb-2">' . htmlspecialchars($institution['type']) . '</span>';
        }
        
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    ' . $type_badge . '
                    <h5 class="card-title">
                        <a href="/vpo/' . htmlspecialchars($institution['id'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($institution['vpo_name'] ?? 'Без названия') . '
                        </a>
                    </h5>
                    <p class="card-text">
                        <i class="fas fa-map-marker-alt text-primary me-1"></i>' . htmlspecialchars($institution['city'] ?? '') . '<br>
                        <small class="text-muted">' . htmlspecialchars($institution['region'] ?? '') . '</small>
                    </p>';
        
        if (!empty($institution['website'])) {
            $greyContent5 .= '<p class="card-text small"><i class="fas fa-globe text-info me-1"></i><a href="' . htmlspecialchars($institution['website']) . '" target="_blank">Официальный сайт</a></p>';
        }
        
        $greyContent5 .= '
                    <a href="/vpo/' . htmlspecialchars($institution['id'] ?? '') . '" class="btn btn-sm btn-outline-primary">Подробнее</a>
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
        
        upload_file(ftp, vpo_page, 'vpo-all-regions-real.php')
        print("   ✅ VPO page updated with DB content")
        
        # 3. Update SPO page
        print("\n3️⃣ Updating SPO page...")
        spo_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'СПО всех регионов России';
$spo = [];
$types = [];

// Get current page
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 24;
$offset = ($current_page - 1) * $per_page;
$total_spo = 0;

if ($connection) {
    try {
        // Get total count
        $result = $connection->query("SELECT COUNT(*) as total FROM spo");
        $total_spo = $result ? $result->fetch_assoc()['total'] : 0;
        
        // Get SPOs for current page
        $stmt = $connection->prepare("SELECT id, spo_name, city, region, address, phone, website, type FROM spo ORDER BY spo_name ASC LIMIT ? OFFSET ?");
        if ($stmt) {
            $stmt->bind_param("ii", $per_page, $offset);
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

$total_pages = ceil($total_spo / $per_page);

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
            <h1 class="display-4 mb-2">🏢 Учреждения СПО России</h1>
            <p class="lead mb-4">Колледжи, техникумы, училища - среднее профессиональное образование</p>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-primary">' . number_format($total_spo) . '</h3>
                            <p class="mb-0">Всего учреждений СПО</p>
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

if (!empty($spo)) {
    foreach ($spo as $institution) {
        $type_badge = '';
        if (!empty($institution['type'])) {
            $type_color = 'primary';
            if (stripos($institution['type'], 'колледж') !== false) $type_color = 'success';
            elseif (stripos($institution['type'], 'техникум') !== false) $type_color = 'info';
            elseif (stripos($institution['type'], 'училище') !== false) $type_color = 'warning';
            
            $type_badge = '<span class="badge bg-' . $type_color . ' mb-2">' . htmlspecialchars($institution['type']) . '</span>';
        }
        
        $greyContent5 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    ' . $type_badge . '
                    <h5 class="card-title">
                        <a href="/spo/' . htmlspecialchars($institution['id'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($institution['spo_name'] ?? 'Без названия') . '
                        </a>
                    </h5>
                    <p class="card-text">
                        <i class="fas fa-map-marker-alt text-primary me-1"></i>' . htmlspecialchars($institution['city'] ?? '') . '<br>
                        <small class="text-muted">' . htmlspecialchars($institution['region'] ?? '') . '</small>
                    </p>';
        
        if (!empty($institution['address'])) {
            $greyContent5 .= '<p class="card-text small"><i class="fas fa-home text-secondary me-1"></i>' . htmlspecialchars($institution['address']) . '</p>';
        }
        
        $greyContent5 .= '
                    <a href="/spo/' . htmlspecialchars($institution['id'] ?? '') . '" class="btn btn-sm btn-outline-primary">Подробнее</a>
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

// Pagination
if ($total_pages > 1) {
    $greyContent5 .= '
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="Страницы">
                    <ul class="pagination justify-content-center">';
    
    $greyContent5 .= '<li class="page-item ' . ($current_page <= 1 ? 'disabled' : '') . '">
        <a class="page-link" href="/spo-all-regions?page=' . ($current_page - 1) . '">Предыдущая</a>
    </li>';
    
    for ($i = 1; $i <= min($total_pages, 10); $i++) {
        $greyContent5 .= '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '">
            <a class="page-link" href="/spo-all-regions?page=' . $i . '">' . $i . '</a>
        </li>';
    }
    
    $greyContent5 .= '<li class="page-item ' . ($current_page >= $total_pages ? 'disabled' : '') . '">
        <a class="page-link" href="/spo-all-regions?page=' . ($current_page + 1) . '">Следующая</a>
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
        
        upload_file(ftp, spo_page, 'spo-all-regions-real.php')
        print("   ✅ SPO page updated with DB content")
        
        ftp.quit()
        
        print("\n✅ ALL PAGES NOW HAVE DATABASE CONTENT!")
        print("\n📊 Updated pages with DB content:")
        print("• Schools - Shows schools with addresses, phones")
        print("• VPO - Shows universities with types and websites")
        print("• SPO - Shows colleges/technical schools with details")
        print("• All pages have pagination (24-30 items per page)")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()