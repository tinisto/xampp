#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def check_columns():
    """Check the actual column names in VPO and schools tables"""
    
    column_check_content = '''<?php
// Check actual column names in database tables
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<style>body { font-family: Arial; margin: 20px; } .success { color: green; } .error { color: red; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }</style>";
echo "<h1>Database Table Columns Check</h1>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if ($connection) {
    echo "<p class='success'>✓ Database connected</p>";
    
    // Check VPO table columns
    echo "<h2>VPO Table Structure:</h2>";
    $result = mysqli_query($connection, "DESCRIBE vpo");
    if ($result) {
        echo "<table><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td></tr>";
        }
        echo "</table>";
        
        // Try to get sample data to understand structure
        echo "<h3>Sample VPO Data (first 3 rows):</h3>";
        $result = mysqli_query($connection, "SELECT * FROM vpo LIMIT 3");
        if ($result) {
            $columns = mysqli_fetch_fields($result);
            echo "<table><tr>";
            foreach ($columns as $col) {
                echo "<th>" . htmlspecialchars($col->name) . "</th>";
            }
            echo "</tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars(substr($value, 0, 50)) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p class='error'>✗ Could not describe VPO table</p>";
    }
    
    // Check Schools table columns
    echo "<h2>Schools Table Structure:</h2>";
    $result = mysqli_query($connection, "DESCRIBE schools");
    if ($result) {
        echo "<table><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td></tr>";
        }
        echo "</table>";
        
        // Try to get sample data
        echo "<h3>Sample Schools Data (first 3 rows):</h3>";
        $result = mysqli_query($connection, "SELECT * FROM schools LIMIT 3");
        if ($result) {
            $columns = mysqli_fetch_fields($result);
            echo "<table><tr>";
            foreach ($columns as $col) {
                echo "<th>" . htmlspecialchars($col->name) . "</th>";
            }
            echo "</tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars(substr($value, 0, 50)) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p class='error'>✗ Could not describe schools table</p>";
    }
    
} else {
    echo "<p class='error'>✗ Database connection failed</p>";
}
?>'''

    try:
        print("Connecting to FTP...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Upload column check
        with open('temp_columns.php', 'w', encoding='utf-8') as f:
            f.write(column_check_content)
        
        with open('temp_columns.php', 'rb') as f:
            ftp.storbinary('STOR check-table-columns.php', f)
        
        print("✓ Uploaded check-table-columns.php")
        
        # Clean up
        os.remove('temp_columns.php')
        ftp.quit()
        
        print("\nColumn Check URL: https://11klassniki.ru/check-table-columns.php")
        
    except Exception as e:
        print(f"✗ Error: {e}")

if __name__ == "__main__":
    print("=== Checking Database Table Columns ===")
    check_columns()