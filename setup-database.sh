#!/bin/bash

echo "11klassniki.ru Database Setup Script"
echo "===================================="
echo ""

# Check if XAMPP MySQL is running
if ! pgrep -f "mysqld" > /dev/null; then
    echo "⚠️  MySQL is not running!"
    echo ""
    echo "Please start XAMPP MySQL:"
    echo "1. Open XAMPP Control Panel"
    echo "2. Click 'Start' next to MySQL"
    echo ""
    echo "Or run from terminal:"
    echo "sudo /Applications/XAMPP/xamppfiles/xampp startmysql"
    echo ""
    exit 1
fi

echo "✅ MySQL is running"
echo ""
echo "Now you can:"
echo "1. Import database: http://localhost:8000/import-database.php"
echo "2. Test connection: http://localhost:8000/test-db-connection-new.php"
echo ""
echo "The import will create database: 11klassniki_claude"