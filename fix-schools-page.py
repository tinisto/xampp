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
    print("🔧 FIXING SCHOOLS PAGE")
    print("Convert to use template system directly")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create working schools page using template
        schools_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'Школы всех регионов России';
$schools = [];

// Get schools from database
if ($connection) {
    try {
        $stmt = $connection->prepare("SELECT id, school_name, city, region FROM schools ORDER BY school_name ASC LIMIT 50");
        if ($stmt) {
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

// Template content sections
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
            <h1 class="display-4 mb-4">🏫 Школы всех регионов России</h1>
            <p class="lead">Найдено школ: ' . count($schools) . '</p>
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
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/school/' . htmlspecialchars($school['id'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($school['school_name'] ?? 'Без названия') . '
                        </a>
                    </h5>
                    <p class="card-text">
                        <i class="fas fa-map-marker-alt me-1"></i>' . htmlspecialchars($school['city'] ?? '') . '
                        <br><small class="text-muted">' . htmlspecialchars($school['region'] ?? '') . '</small>
                    </p>
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

$greyContent6 = '';

// Use template system
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        # Upload fixed schools page
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(schools_page)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR schools-all-regions-real.php', file)
        os.unlink(tmp_path)
        
        print("   ✅ Fixed schools page to use template system")
        
        ftp.quit()
        
        print(f"\n✅ SCHOOLS PAGE FIXED!")
        
        print(f"\n🎯 Schools page now uses template system like news/category pages")
        
        print(f"\n🧪 Test page:")
        print(f"• https://11klassniki.ru/schools-all-regions")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()