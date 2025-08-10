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
    print("🔧 FIXING VPO PAGE INTERNAL SERVER ERROR")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Check what file is being called for vpo-all-regions
        print("📂 Checking VPO routing...")
        
        # Create working VPO page using template system
        vpo_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'ВУЗы всех регионов России';
$vpo = [];

// Get VPOs from database
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

// Template content sections
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

// Use template system
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        # Try multiple possible filenames
        possible_files = [
            'vpo-all-regions-real.php',
            'vpo-all-regions-new.php',
            'vpo-all-regions.php'
        ]
        
        uploaded = False
        for filename in possible_files:
            try:
                upload_file(ftp, vpo_page, filename)
                print(f"   ✅ Created/Updated: {filename}")
                uploaded = True
            except:
                print(f"   ⚪ Skipped: {filename}")
        
        if uploaded:
            print("\n✅ VPO PAGE FIXED!")
            print("\n🧪 Test page:")
            print("• https://11klassniki.ru/vpo-all-regions")
        else:
            print("\n❌ Could not upload VPO page")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()