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
    print("📊 CREATING DATA LOADING SUMMARY PAGE")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create summary page
        summary_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'Data Loading Status';

// Check database counts
$counts = [];
if ($connection) {
    $tables = [
        'schools' => 'Школы',
        'vpo' => 'ВУЗы', 
        'spo' => 'СПО',
        'posts' => 'Новости/Статьи',
        'users' => 'Пользователи',
        'comments' => 'Комментарии'
    ];
    
    foreach ($tables as $table => $label) {
        $result = mysqli_query($connection, "SELECT COUNT(*) as c FROM $table");
        if ($result) {
            $counts[$table] = mysqli_fetch_assoc($result)['c'];
        } else {
            $counts[$table] = 0;
        }
    }
}

$greyContent1 = '
<div class="container mt-4">
    <h1>📊 Data Loading Status - All Pages</h1>
    <p class="text-muted">Status of database content across the site</p>
</div>';

$greyContent2 = '
<div class="container">
    <h2>Database Statistics</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Table</th>
                <th>Count</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

foreach ($counts as $table => $count) {
    $status = $count > 0 ? '<span class="badge bg-success">✓ Has Data</span>' : '<span class="badge bg-warning">⚠ Empty</span>';
    $greyContent2 .= "
            <tr>
                <td>$table</td>
                <td>" . number_format($count) . "</td>
                <td>$status</td>
            </tr>";
}

$greyContent2 .= '
        </tbody>
    </table>
</div>';

$greyContent3 = '
<div class="container">
    <h2>Pages Loading Database Content</h2>
    <div class="row">';

$pages = [
    [
        'title' => 'Homepage',
        'url' => '/',
        'loads' => 'schools, vpo, spo, posts counts + recent news',
        'status' => $counts['posts'] > 0 ? 'working' : 'no-data'
    ],
    [
        'title' => 'News Page',
        'url' => '/news',
        'loads' => 'posts table (12 per page)',
        'status' => $counts['posts'] > 0 ? 'working' : 'no-data'
    ],
    [
        'title' => 'Schools Directory',
        'url' => '/schools-all-regions',
        'loads' => 'schools table (30 per page)',
        'status' => $counts['schools'] > 0 ? 'working' : 'no-data'
    ],
    [
        'title' => 'VPO Directory',
        'url' => '/vpo-all-regions',
        'loads' => 'vpo table (24 per page)',
        'status' => $counts['vpo'] > 0 ? 'working' : 'no-data'
    ],
    [
        'title' => 'SPO Directory',
        'url' => '/spo-all-regions',
        'loads' => 'spo table (24 per page)',
        'status' => $counts['spo'] > 0 ? 'working' : 'no-data'
    ],
    [
        'title' => 'Search',
        'url' => '/search?q=test',
        'loads' => 'schools, vpo, spo, posts (search)',
        'status' => 'working'
    ],
    [
        'title' => 'Category: Abiturientam',
        'url' => '/category/abiturientam',
        'loads' => 'posts by category',
        'status' => $counts['posts'] > 0 ? 'working' : 'no-data'
    ],
    [
        'title' => 'Category: 11-klassniki',
        'url' => '/category/11-klassniki',
        'loads' => 'posts by category',
        'status' => $counts['posts'] > 0 ? 'working' : 'no-data'
    ],
    [
        'title' => 'Category: Education News',
        'url' => '/category/education-news',
        'loads' => 'posts by category',
        'status' => $counts['posts'] > 0 ? 'working' : 'no-data'
    ],
    [
        'title' => 'Dashboard: Comments',
        'url' => '/dashboard-moderation.php',
        'loads' => 'comments table',
        'status' => $counts['comments'] > 0 ? 'working' : 'no-data'
    ]
];

foreach ($pages as $page) {
    $statusBadge = $page['status'] === 'working' 
        ? '<span class="badge bg-success">Working</span>' 
        : '<span class="badge bg-warning">No Data</span>';
    
    $greyContent3 .= '
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">' . $page['title'] . '</h5>
                    <p class="card-text">
                        <strong>URL:</strong> <a href="' . $page['url'] . '" target="_blank">' . $page['url'] . '</a><br>
                        <strong>Loads:</strong> ' . $page['loads'] . '<br>
                        <strong>Status:</strong> ' . $statusBadge . '
                    </p>
                </div>
            </div>
        </div>';
}

$greyContent3 .= '
    </div>
</div>';

$greyContent4 = '
<div class="container mb-5">
    <h2>Debug Information</h2>
    <div class="alert alert-info">
        <h4>If pages show 0 data:</h4>
        <ol>
            <li>Check if database tables are empty (see counts above)</li>
            <li>Verify database connection is working</li>
            <li>Check error logs for query issues</li>
            <li>Ensure table names match (vpo not vuz, posts not news)</li>
        </ol>
    </div>
    
    <h3>Quick Links to Check:</h3>
    <ul>
        <li><a href="/" target="_blank">Homepage</a> - Should show 4 statistics boxes</li>
        <li><a href="/news" target="_blank">News</a> - Should show news count and articles</li>
        <li><a href="/vpo-all-regions" target="_blank">VPO</a> - Should show universities count</li>
        <li><a href="/spo-all-regions" target="_blank">SPO</a> - Should show colleges count</li>
        <li><a href="/schools-all-regions" target="_blank">Schools</a> - Should show schools count</li>
        <li><a href="/search?q=test" target="_blank">Search</a> - Should search all tables</li>
    </ul>
</div>';

$greyContent5 = '';
$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, summary_page, 'data-loading-status.php')
        print("   ✅ Created data loading status page")
        
        ftp.quit()
        
        print("\n✅ DATA LOADING SUMMARY PAGE CREATED!")
        print("\n🎯 Visit: https://11klassniki.ru/data-loading-status.php")
        print("\nThis page shows:")
        print("• Database table counts")
        print("• Which pages load data")
        print("• Current status of each page")
        print("• Debug information")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()