#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def debug_pages():
    """Create debug page to check what's happening with educational pages"""
    
    debug_content = '''<?php
// Debug Educational Pages - Check routing and templates
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Educational Pages Debug</h1>";

// Check VPO routing
echo "<h2>VPO Page Analysis</h2>";
$vpoFile = $_SERVER['DOCUMENT_ROOT'] . '/vpo-all-regions-new.php';
if (file_exists($vpoFile)) {
    echo "<p style='color: green;'>✓ vpo-all-regions-new.php exists</p>";
    echo "<p>File size: " . filesize($vpoFile) . " bytes</p>";
    
    // Show first 500 characters of content
    $content = file_get_contents($vpoFile);
    echo "<h3>File Content (first 500 chars):</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars(substr($content, 0, 500)) . "...</pre>";
} else {
    echo "<p style='color: red;'>✗ vpo-all-regions-new.php not found</p>";
}

// Check Schools routing  
echo "<h2>Schools Page Analysis</h2>";
$schoolsFile = $_SERVER['DOCUMENT_ROOT'] . '/schools-all-regions-real.php';
if (file_exists($schoolsFile)) {
    echo "<p style='color: green;'>✓ schools-all-regions-real.php exists</p>";
    echo "<p>File size: " . filesize($schoolsFile) . " bytes</p>";
    
    // Show first 500 characters of content
    $content = file_get_contents($schoolsFile);
    echo "<h3>File Content (first 500 chars):</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars(substr($content, 0, 500)) . "...</pre>";
} else {
    echo "<p style='color: red;'>✗ schools-all-regions-real.php not found</p>";
}

// Check database connection
echo "<h2>Database Test</h2>";
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if ($connection) {
    echo "<p style='color: green;'>✓ Database connected</p>";
    
    // Test VPO table
    $result = mysqli_query($connection, "SELECT COUNT(*) as c FROM vpo LIMIT 1");
    if ($result) {
        $count = mysqli_fetch_assoc($result)['c'];
        echo "<p>VPO table records: <strong>$count</strong></p>";
    }
    
    // Test Schools table
    $result = mysqli_query($connection, "SELECT COUNT(*) as c FROM schools LIMIT 1");
    if ($result) {
        $count = mysqli_fetch_assoc($result)['c'];
        echo "<p>Schools table records: <strong>$count</strong></p>";
    }
    
    // Test VPO regions
    $result = mysqli_query($connection, "SELECT COUNT(DISTINCT region_vpo) as c FROM vpo");
    if ($result) {
        $count = mysqli_fetch_assoc($result)['c'];
        echo "<p>VPO regions: <strong>$count</strong></p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
}

// Check .htaccess rules
echo "<h2>.htaccess Rules</h2>";
$htaccessFile = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';
if (file_exists($htaccessFile)) {
    $htaccess = file_get_contents($htaccessFile);
    $lines = explode("\\n", $htaccess);
    
    echo "<h3>Educational Pages Rules:</h3>";
    foreach ($lines as $i => $line) {
        if (stripos($line, 'vpo-all-regions') !== false || stripos($line, 'schools-all-regions') !== false) {
            echo "<p>" . ($i + 1) . ": " . htmlspecialchars($line) . "</p>";
        }
    }
}

// Test direct includes
echo "<h2>Direct Template Test</h2>";
try {
    echo "<h3>Testing VPO Template:</h3>";
    $_GET['type'] = 'vpo';
    $institutionType = 'vpo';
    
    ob_start();
    include $_SERVER['DOCUMENT_ROOT'] . '/vpo-all-regions-new.php';
    $output = ob_get_clean();
    
    echo "<p>Template output length: " . strlen($output) . " characters</p>";
    if (strpos($output, 'Данные загружаются') !== false) {
        echo "<p style='color: red;'>⚠️ Still shows loading message</p>";
    }
    if (strpos($output, '2520') !== false || strpos($output, '2,520') !== false) {
        echo "<p style='color: green;'>✓ Shows correct VPO count</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error testing VPO template: " . $e->getMessage() . "</p>";
}
?>'''

    try:
        print("Connecting to FTP...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Upload debug page
        with open('temp_debug.php', 'w', encoding='utf-8') as f:
            f.write(debug_content)
        
        with open('temp_debug.php', 'rb') as f:
            ftp.storbinary('STOR debug-educational-pages.php', f)
        
        print("✓ Uploaded debug-educational-pages.php")
        
        # Clean up
        os.remove('temp_debug.php')
        ftp.quit()
        
        print("\nDebug URL: https://11klassniki.ru/debug-educational-pages.php")
        
    except Exception as e:
        print(f"✗ Error: {e}")

if __name__ == "__main__":
    print("=== Creating Educational Pages Debug ===")
    debug_pages()