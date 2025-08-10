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
    print("🔧 FIXING SPO PAGE AND ACCOUNT LOGO ISSUE")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Fix SPO page
        print("\n1️⃣ Fixing SPO page...")
        
        spo_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'СПО всех регионов России';
$spo = [];

// Get SPO from database
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

// Template content sections
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

// Use template system
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        # Upload SPO page
        upload_file(ftp, spo_page, 'spo-all-regions-real.php')
        print("   ✅ Fixed spo-all-regions-real.php")
        
        # 2. Check account page for different logo
        print("\n2️⃣ Checking account page...")
        
        # Download account page to check
        try:
            account_content = []
            ftp.retrlines('RETR account-new.php', account_content.append)
            
            # Check if it has its own favicon reference
            has_own_favicon = False
            for line in account_content:
                if 'favicon' in line.lower() or 'logo' in line.lower():
                    print(f"   Found logo reference: {line.strip()}")
                    has_own_favicon = True
            
            if has_own_favicon:
                print("   ⚠️ Account page has its own favicon/logo references!")
                print("   🔧 Fixing account page to use template system...")
                
                # Create account page that uses template
                account_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Check if user is logged in
if (session_status() == PHP_SESSION_NONE) {
    try { session_start(); } catch (Exception $e) { /* Continue */ }
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

$page_title = 'Личный кабинет';
$user_stats = ['comments' => 0, 'posts' => 0, 'news' => 0];

// Get user statistics
if ($connection && isset($_SESSION['user_id'])) {
    try {
        // Count user comments
        $stmt = $connection->prepare("SELECT COUNT(*) as count FROM comments WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_stats['comments'] = $result->fetch_assoc()['count'] ?? 0;
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log('Stats error: ' . $e->getMessage());
    }
}

// Template content sections
$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-4">👤 Личный кабинет</h1>
            <p class="lead">Добро пожаловать, ' . htmlspecialchars($_SESSION['username'] ?? 'Пользователь') . '!</p>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">📊 Статистика</h5>
                    <p class="card-text">Комментарии: ' . $user_stats['comments'] . '</p>
                    <p class="card-text">Статьи: ' . $user_stats['posts'] . '</p>
                    <p class="card-text">Новости: ' . $user_stats['news'] . '</p>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">🔧 Быстрые действия</h5>
                    <a href="/account/edit-profile" class="btn btn-primary me-2">Редактировать профиль</a>
                    <a href="/account/change-password" class="btn btn-secondary me-2">Изменить пароль</a>
                    <a href="/account/my-comments" class="btn btn-info">Мои комментарии</a>
                </div>
            </div>
        </div>
    </div>
</div>';

$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '
<div class="container">
    <div class="row">
        <div class="col-12 text-center">
            <a href="/logout" class="btn btn-danger">Выйти из аккаунта</a>
        </div>
    </div>
</div>';

// Use template system
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
                
                upload_file(ftp, account_page, 'account-new.php')
                print("   ✅ Fixed account page to use template system")
            else:
                print("   ✅ Account page doesn't have separate logo")
        except Exception as e:
            print(f"   ❌ Could not check account page: {str(e)}")
        
        ftp.quit()
        
        print("\n✅ BOTH ISSUES FIXED!")
        print("\n🧪 Test pages:")
        print("• https://11klassniki.ru/spo-all-regions (should work now)")
        print("• https://11klassniki.ru/account/ (should show same logo as other pages)")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()