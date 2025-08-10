#!/bin/bash

echo "Simple XAMPP MySQL Fix"
echo "====================="
echo ""
echo "This will reset MySQL to work with no password"
echo ""

# Option 1: Try XAMPP's built-in security script
echo "Option 1: Using XAMPP security reset"
echo "When prompted:"
echo "- Enter 'no' for XAMPP security"
echo "- Enter blank (just press Enter) for MySQL root password"
echo ""
echo "Running security script..."
echo ""

sudo /Applications/XAMPP/xamppfiles/xampp security

echo ""
echo "If that worked, try: http://localhost:8000/import-database.php"
echo ""
echo "If not, press Enter to try Option 2..."
read

# Option 2: Direct approach
echo ""
echo "Option 2: Direct MySQL reset"
echo ""

# Stop everything
sudo /Applications/XAMPP/xamppfiles/xampp stopmysql
sleep 2

# Create init file to reset password
cat > /tmp/mysql-init.txt <<'EOF'
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
ALTER USER 'root'@'127.0.0.1' IDENTIFIED BY '';
FLUSH PRIVILEGES;
EOF

echo "Starting MySQL with init file..."
sudo /Applications/XAMPP/xamppfiles/bin/mysqld --init-file=/tmp/mysql-init.txt &
sleep 5

echo "Stopping MySQL..."
sudo pkill mysqld
rm -f /tmp/mysql-init.txt

echo ""
echo "Done! Now:"
echo "1. Start MySQL from XAMPP Control Panel"
echo "2. Visit: http://localhost:8000/import-database.php"