#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def create_direct_test():
    """Create a direct test page that bypasses routing to see the actual issue"""
    
    direct_test_content = '''<?php
// Direct VPO Test - Bypass all routing
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<style>body { font-family: Arial; margin: 20px; } .success { color: green; } .error { color: red; } .info { color: blue; }</style>";
echo "<h1>Direct VPO Test</h1>";

// Test database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if ($connection) {
    echo "<p class='success'>✓ Database connected successfully</p>";
    
    // Get VPO data directly
    $query = "SELECT region_vpo as region, COUNT(*) as count FROM vpo GROUP BY region_vpo ORDER BY region_vpo LIMIT 5";
    $result = mysqli_query($connection, $query);
    
    if ($result) {
        echo "<p class='success'>✓ Query executed successfully</p>";
        echo "<h3>Sample VPO Regions:</h3>";
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;'>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            $regionUrl = strtolower(str_replace([' ', 'ё'], ['-', 'е'], $row['region']));
            echo '<div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h4 style="margin: 0 0 10px 0; color: #333;">' . htmlspecialchars($row['region']) . '</h4>
                    <div style="color: #28a745; font-weight: 600;">
                        ' . $row['count'] . ' ВУЗов
                    </div>
                  </div>';
        }
        echo "</div>";
        
        // Get total count
        $result = mysqli_query($connection, "SELECT COUNT(*) as total FROM vpo");
        if ($result) {
            $total = mysqli_fetch_assoc($result)['total'];
            echo "<p class='info'>Total VPO institutions: <strong>" . number_format($total) . "</strong></p>";
        }
        
    } else {
        echo "<p class='error'>✗ Query failed: " . mysqli_error($connection) . "</p>";
    }
    
} else {
    echo "<p class='error'>✗ Database connection failed</p>";
}

// Now test what happens when we include the actual router
echo "<hr><h2>Testing Router File</h2>";
echo "<p>Testing what the router file produces...</p>";

try {
    // Set the type parameter
    $_GET['type'] = 'vpo';
    $institutionType = 'vpo';
    
    // Capture output from the router
    ob_start();
    include $_SERVER['DOCUMENT_ROOT'] . '/vpo-all-regions-new.php';
    $router_output = ob_get_clean();
    
    echo "<p class='info'>Router output length: " . strlen($router_output) . " characters</p>";
    
    // Check for specific content
    if (strpos($router_output, 'Данные загружаются') !== false) {
        echo "<p class='error'>⚠️ Router still shows 'Данные загружаются'</p>";
    }
    
    if (strpos($router_output, '2520') !== false || strpos($router_output, '2,520') !== false) {
        echo "<p class='success'>✓ Router shows correct count</p>";
    }
    
    if (strpos($router_output, 'ВУЗов') !== false) {
        echo "<p class='success'>✓ Router shows VPO content</p>";
    }
    
    // Show first 1000 characters of router output
    echo "<h3>Router Output Sample:</h3>";
    echo "<textarea style='width: 100%; height: 300px;'>" . htmlspecialchars(substr($router_output, 0, 1000)) . "</textarea>";
    
} catch (Exception $e) {
    echo "<p class='error'>Error testing router: " . $e->getMessage() . "</p>";
}

?>'''

    try:
        print("Connecting to FTP...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Upload direct test
        with open('temp_direct_test.php', 'w', encoding='utf-8') as f:
            f.write(direct_test_content)
        
        with open('temp_direct_test.php', 'rb') as f:
            ftp.storbinary('STOR test-vpo-direct.php', f)
        
        print("✓ Uploaded test-vpo-direct.php")
        
        # Clean up
        os.remove('temp_direct_test.php')
        ftp.quit()
        
        print("\nDirect Test URL: https://11klassniki.ru/test-vpo-direct.php")
        
    except Exception as e:
        print(f"✗ Error: {e}")

if __name__ == "__main__":
    print("=== Creating Direct VPO Test ===")
    create_direct_test()