#!/bin/bash

echo "MySQL Import (No Foreign Key Checks)"
echo "===================================="
echo ""

# Variables
MYSQL_BIN="/Applications/XAMPP/xamppfiles/bin/mysql"
SQL_FILE="/Users/anatolys/Downloads/custsql-ipg117_eigbox_net.sql"
DB_HOST="127.0.0.1"
DB_USER="root"
DB_PASS="root"
DB_NAME="11klassniki_claude"

echo "Importing: $(basename "$SQL_FILE")"
echo ""

# Create database
echo "1. Creating database..."
$MYSQL_BIN -h $DB_HOST -u $DB_USER -p$DB_PASS -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo "✓ Database created"

# Create temporary SQL file with FK checks disabled
echo ""
echo "2. Preparing import file..."
TEMP_SQL="/tmp/import_no_fk.sql"

# Add commands to disable FK checks at the beginning
echo "SET FOREIGN_KEY_CHECKS=0;" > "$TEMP_SQL"
echo "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';" >> "$TEMP_SQL"
echo "SET AUTOCOMMIT=0;" >> "$TEMP_SQL"
echo "START TRANSACTION;" >> "$TEMP_SQL"

# Add the original SQL file content
cat "$SQL_FILE" >> "$TEMP_SQL"

# Add commands to re-enable FK checks at the end
echo "" >> "$TEMP_SQL"
echo "COMMIT;" >> "$TEMP_SQL"
echo "SET FOREIGN_KEY_CHECKS=1;" >> "$TEMP_SQL"

echo "✓ Prepared import file"

# Import
echo ""
echo "3. Importing data (this will take 2-3 minutes)..."
echo "   Please be patient..."

$MYSQL_BIN -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < "$TEMP_SQL"

if [ $? -eq 0 ]; then
    echo ""
    echo "✓ Import completed successfully!"
    
    # Clean up
    rm -f "$TEMP_SQL"
    
    # Show statistics
    echo ""
    echo "4. Database statistics:"
    
    # Count tables
    TABLE_COUNT=$($MYSQL_BIN -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME';")
    echo "   Total tables: $TABLE_COUNT"
    
    # Show record counts
    echo ""
    echo "   Record counts:"
    for table in users posts schools vpo spo comments news categories regions towns; do
        COUNT=$($MYSQL_BIN -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME -N -e "SELECT COUNT(*) FROM $table;" 2>/dev/null)
        if [ $? -eq 0 ] && [ -n "$COUNT" ]; then
            printf "   %-15s %s\n" "$table:" "$COUNT"
        fi
    done
    
    echo ""
    echo "✓ Database import complete!"
    echo ""
    echo "Test your site:"
    echo "- http://localhost:8000/test-db-connection-new.php"
    echo "- http://localhost:8000/"
else
    echo ""
    echo "✗ Import failed!"
    # Clean up
    rm -f "$TEMP_SQL"
fi