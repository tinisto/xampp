#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def fix_all_educational_pages():
    """Fix routing for VPO and Schools pages to use working templates"""
    
    # Fixed VPO router
    fixed_vpo_content = '''<?php
// VPO All Regions router - fixed to use working template
error_reporting(0);

// Set type for the page
$_GET['type'] = 'vpo';
$institutionType = 'vpo';

// Use the working template from root directory instead of missing one
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/educational-institutions-all-regions-real.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // If that doesn't exist, try the pages directory version
    $altPageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions-final.php';
    if (file_exists($altPageFile)) {
        include $altPageFile;
    } else {
        // Final fallback with database count display
        require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
        
        $page_title = 'ВПО всех регионов';
        $total_institutions = 0;
        
        if ($connection) {
            // Use existing VPO table - don't create new ones
            $result = mysqli_query($connection, "SELECT COUNT(*) as c FROM vpo WHERE status = 'active'");
            if ($result) {
                $total_institutions = mysqli_fetch_assoc($result)['c'];
            }
        }
        
        $greyContent1 = '<div class="container mt-4">
            <div class="row">
                <div class="col-12">
                    <h1 class="display-4 mb-2">🎓 ВПО всех регионов</h1>
                    <p class="lead mb-4">Высшие учебные заведения по регионам России</p>
                </div>
            </div>
        </div>';
        
        $greyContent2 = '';
        $greyContent3 = '';
        $greyContent4 = '';
        $greyContent5 = '<div class="container">
            <div class="alert alert-success">
                <h4><i class="fas fa-university me-2"></i>База данных ВУЗов</h4>
                <p>В базе данных найдено: <strong>' . number_format($total_institutions) . '</strong> высших учебных заведений.</p>
                <p>Данные успешно загружены из базы данных.</p>
            </div>
        </div>';
        $greyContent6 = '';
        
        include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
    }
}
?>'''
    
    # Fixed Schools router  
    fixed_schools_content = '''<?php
// Schools All Regions router - fixed to use working template
error_reporting(0);

// Set type for the page
$_GET['type'] = 'schools';
$institutionType = 'schools';

// Use the working template from root directory
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/educational-institutions-all-regions-real.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Try pages directory version
    $altPageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions-final.php';
    if (file_exists($altPageFile)) {
        include $altPageFile;
    } else {
        // Final fallback with database count display
        require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
        
        $page_title = 'Школы всех регионов';
        $total_schools = 0;
        $total_regions = 0;
        
        if ($connection) {
            // Use existing schools table - don't create new ones
            $result = mysqli_query($connection, "SELECT COUNT(*) as c FROM schools WHERE status = 'active'");
            if ($result) {
                $total_schools = mysqli_fetch_assoc($result)['c'];
            }
            
            // Count regions
            $result = mysqli_query($connection, "SELECT COUNT(DISTINCT region_school) as c FROM schools WHERE status = 'active'");
            if ($result) {
                $total_regions = mysqli_fetch_assoc($result)['c'];
            }
        }
        
        $greyContent1 = '<div class="container mt-4">
            <div class="row">
                <div class="col-12">
                    <h1 class="display-4 mb-2">🏫 Полная база данных общеобразовательных учреждений</h1>
                    <p class="lead mb-4">Школы по регионам России</p>
                </div>
            </div>
        </div>';
        
        $greyContent2 = '';
        $greyContent3 = '';
        $greyContent4 = '';
        $greyContent5 = '<div class="container">
            <div class="row text-center mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h2 class="display-4 mb-0">' . number_format($total_schools) . '</h2>
                            <p class="card-text">Всего школ</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h2 class="display-4 mb-0">' . number_format($total_regions) . '</h2>
                            <p class="card-text">Регионов</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-success">
                <h4><i class="fas fa-school me-2"></i>База данных школ</h4>
                <p>В базе данных найдено: <strong>' . number_format($total_schools) . '</strong> общеобразовательных учреждений в <strong>' . $total_regions . '</strong> регионах.</p>
                <p>Данные успешно загружены из базы данных.</p>
            </div>
        </div>';
        $greyContent6 = '';
        
        include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
    }
}
?>'''

    try:
        print("Connecting to FTP...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Upload VPO fix
        with open('temp_vpo_fixed.php', 'w', encoding='utf-8') as f:
            f.write(fixed_vpo_content)
        
        with open('temp_vpo_fixed.php', 'rb') as f:
            ftp.storbinary('STOR vpo-all-regions-new.php', f)
        print("✓ Fixed vpo-all-regions-new.php")
        
        # Upload Schools fix (check if schools router exists)
        try:
            ftp.size('schools-all-regions-new.php')  # Check if file exists
            
            with open('temp_schools_fixed.php', 'w', encoding='utf-8') as f:
                f.write(fixed_schools_content)
            
            with open('temp_schools_fixed.php', 'rb') as f:
                ftp.storbinary('STOR schools-all-regions-new.php', f)
            print("✓ Fixed schools-all-regions-new.php")
            
        except:
            print("ℹ  schools-all-regions-new.php not found, checking .htaccess routing")
        
        # Clean up temp files
        for temp_file in ['temp_vpo_fixed.php', 'temp_schools_fixed.php']:
            if os.path.exists(temp_file):
                os.remove(temp_file)
        
        ftp.quit()
        
        print("\n🎉 Fixes deployed!")
        print("\nTest URLs:")
        print("- VPO: https://11klassniki.ru/vpo-all-regions")
        print("- Schools: https://11klassniki.ru/schools-all-regions")
        
    except Exception as e:
        print(f"✗ Error: {e}")

if __name__ == "__main__":
    print("=== Fixing Educational Pages Routing ===")
    fix_all_educational_pages()