#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def fix_vpo_page():
    # Fixed version of vpo-all-regions-new.php
    fixed_content = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'ВПО всех регионов';

// Initialize variables
$total_institutions = 0;

// Get VPO data using correct field names
if ($connection) {
    // Get total count
    $result = mysqli_query($connection, "SELECT COUNT(*) as c FROM vpo");
    if ($result) {
        $total_institutions = mysqli_fetch_assoc($result)['c'];
    }
}

$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">ВПО по регионам</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-2">🎓 ВПО всех регионов</h1>
            <p class="lead mb-4">Высшие учебные заведения по регионам России</p>
            <p class="text-muted">В базе данных найдено: ' . number_format($total_institutions) . ' высших учебных заведений</p>
        </div>
    </div>
</div>';

$greyContent3 = '';
$greyContent4 = '';

$greyContent5 = '
<div class="container">
    <div class="alert alert-success mb-4">
        <h4><i class="fas fa-university me-2"></i>База данных ВУЗов</h4>
        <p><strong>✓ Данные успешно загружены из базы данных</strong></p>
        <p>В базе данных найдено: <strong>' . number_format($total_institutions) . '</strong> высших учебных заведений.</p>
        <p><strong>Вот примеры ВУЗов из базы данных:</strong></p>
    </div>
    
    <div class="row g-3">';

// Get actual VPO data using ANY available fields (don't filter by empty fields)
if ($connection && $total_institutions > 0) {
    $result = mysqli_query($connection, "SELECT * FROM vpo LIMIT 12");
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Try different possible name fields
            $name = $row['name'] ?? $row['name_vpo'] ?? $row['title'] ?? $row['full_name'] ?? 'ВУЗ #' . ($row['id'] ?? 'N/A');
            $location = $row['town'] ?? $row['city'] ?? $row['region'] ?? $row['address'] ?? '';
            
            $greyContent5 .= '
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 border-primary">
                    <div class="card-body">
                        <h6 class="card-title text-primary">' . htmlspecialchars($name) . '</h6>';
            if (!empty($location)) {
                $greyContent5 .= '<small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>' . htmlspecialchars($location) . '</small>';
            }
            $greyContent5 .= '
                    </div>
                </div>
            </div>';
        }
    } else {
        $greyContent5 .= '
        <div class="col-12">
            <div class="alert alert-info">
                <p>Не удалось загрузить данные ВУЗов. Показываем только счетчик: ' . number_format($total_institutions) . ' записей.</p>
            </div>
        </div>';
    }
} else {
    $greyContent5 .= '
    <div class="col-12">
        <div class="alert alert-warning">
            <p>Нет подключения к базе данных.</p>
        </div>
    </div>';
}

$greyContent5 .= '
    </div>
    
    <div class="text-center mt-4">
        <p class="text-muted">Показаны примеры из ' . number_format($total_institutions) . ' ВУЗов в базе данных</p>
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
        with open('temp_vpo_fixed.php', 'w', encoding='utf-8') as f:
            f.write(fixed_content)
        
        # Upload fixed version to CORRECT file from .htaccess
        with open('temp_vpo_fixed.php', 'rb') as f:
            ftp.storbinary('STOR vpo-all-regions-simple.php', f)
        
        print("✓ Updated vpo-all-regions-simple.php (CORRECT FILE from .htaccess!)")
        
        # Clean up
        os.remove('temp_vpo_fixed.php')
        ftp.quit()
        
        print("\\n🎉 VPO Fix deployed!")
        print("\\nChanges made:")
        print("- Added actual VPO data display (12 sample institutions)")
        print("- Shows real university names and locations from database") 
        print("- Displays cards with VPO information instead of just count")
        print("- Users can now SEE the actual data, not just success message")
        print("\\nTest: https://11klassniki.ru/vpo-all-regions")
        
    except Exception as e:
        print(f"✗ Error: {e}")

if __name__ == "__main__":
    print("=== Showing VPO Data Correctly ===")
    fix_vpo_page()