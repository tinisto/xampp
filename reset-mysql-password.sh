#!/bin/bash

echo "XAMPP MySQL Password Reset Script"
echo "================================="
echo ""
echo "This script will help reset your MySQL root password to blank (no password)"
echo ""

# Step 1: Instructions
echo "Please follow these steps:"
echo ""
echo "1. First, STOP MySQL in XAMPP Control Panel"
echo "   Press Enter when MySQL is stopped..."
read

# Step 2: Start MySQL in safe mode
echo ""
echo "2. Starting MySQL in safe mode (you'll need to enter your macOS password)..."
echo ""
sudo /Applications/XAMPP/xamppfiles/bin/mysqld_safe --skip-grant-tables &

echo ""
echo "Waiting for MySQL to start in safe mode..."
sleep 5

# Step 3: Reset password
echo ""
echo "3. Resetting root password to blank..."
echo ""

/Applications/XAMPP/xamppfiles/bin/mysql -u root <<EOF
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
ALTER USER 'root'@'127.0.0.1' IDENTIFIED BY '';
FLUSH PRIVILEGES;
exit
EOF

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Password reset successful!"
else
    echo ""
    echo "❌ Password reset failed. You may need to do it manually."
fi

# Step 4: Kill the safe mode MySQL
echo ""
echo "4. Stopping MySQL safe mode..."
sudo pkill mysqld

echo ""
echo "5. Now START MySQL again from XAMPP Control Panel"
echo "   Then visit: http://localhost:8000/import-database.php"
echo ""
echo "Done!"