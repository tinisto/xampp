#!/bin/bash

echo "Direct MySQL Import Script"
echo "========================="
echo ""

# Variables
MYSQL_BIN="/Applications/XAMPP/xamppfiles/bin/mysql"
SQL_FILE="/Users/anatolys/Downloads/custsql-ipg117_eigbox_net.sql"
DB_HOST="127.0.0.1"
DB_USER="root"
DB_PASS="root"
DB_NAME="11klassniki_claude"

echo "SQL file: $(basename "$SQL_FILE")"
echo "Size: $(du -h "$SQL_FILE" | cut -f1)"
echo ""

# Step 1: Create database
echo "1. Creating database..."
$MYSQL_BIN -h $DB_HOST -u $DB_USER -p$DB_PASS -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if [ $? -eq 0 ]; then
    echo "✓ Database created: $DB_NAME"
else
    echo "✗ Failed to create database"
    exit 1
fi

# Step 2: Import SQL file
echo ""
echo "2. Importing SQL file (this may take 2-3 minutes)..."
echo "   Please wait..."

# Use pv if available for progress, otherwise just import
if command -v pv &> /dev/null; then
    pv "$SQL_FILE" | $MYSQL_BIN -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME
else
    $MYSQL_BIN -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < "$SQL_FILE"
fi

if [ $? -eq 0 ]; then
    echo ""
    echo "✓ Import completed successfully!"
    
    # Show statistics
    echo ""
    echo "3. Checking imported data..."
    
    # Count tables
    TABLE_COUNT=$($MYSQL_BIN -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME';")
    echo "   Tables: $TABLE_COUNT"
    
    # Show record counts for main tables
    for table in users posts schools vpo spo comments news; do
        COUNT=$($MYSQL_BIN -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME -N -e "SELECT COUNT(*) FROM $table;" 2>/dev/null)
        if [ $? -eq 0 ]; then
            echo "   $table: $COUNT records"
        fi
    done
    
    echo ""
    echo "✓ Database ready!"
    echo ""
    echo "You can now visit:"
    echo "- Test connection: http://localhost:8000/test-db-connection-new.php"
    echo "- Homepage: http://localhost:8000/"
else
    echo ""
    echo "✗ Import failed!"
    echo "Check if MySQL is running and password is correct."
fi