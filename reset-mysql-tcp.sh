#!/bin/bash

echo "XAMPP MySQL Password Reset (TCP/IP Method)"
echo "=========================================="
echo ""

# Step 1: Kill any existing MySQL processes
echo "1. Stopping any running MySQL processes..."
sudo pkill -f mysql
sleep 2

# Step 2: Start MySQL with skip-grant-tables
echo ""
echo "2. Starting MySQL in safe mode..."
echo "   (Enter your macOS password when prompted)"
echo ""

# Start MySQL with networking enabled and skip grant tables
sudo /Applications/XAMPP/xamppfiles/bin/mysqld --skip-grant-tables --bind-address=127.0.0.1 &

echo ""
echo "Waiting for MySQL to start (10 seconds)..."
sleep 10

# Step 3: Reset password using TCP/IP connection
echo ""
echo "3. Resetting root password..."
echo ""

# Use TCP/IP connection explicitly
/Applications/XAMPP/xamppfiles/bin/mysql -h 127.0.0.1 -u root --protocol=TCP <<EOF
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
ALTER USER 'root'@'127.0.0.1' IDENTIFIED BY '';
ALTER USER 'root'@'%' IDENTIFIED BY '';
FLUSH PRIVILEGES;
EOF

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Password reset successful!"
    
    # Update the config file
    cat > /Applications/XAMPP/xamppfiles/htdocs/config/database.local.php <<'EOF'
<?php
// Local database configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', '11klassniki_claude');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// Environment flag
define('IS_LOCAL', true);

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Timezone
date_default_timezone_set('Europe/Moscow');
?>
EOF
    echo "✅ Updated database configuration"
else
    echo ""
    echo "❌ Password reset failed."
fi

# Step 4: Kill MySQL
echo ""
echo "4. Stopping MySQL safe mode..."
sudo pkill -f mysqld
sleep 2

echo ""
echo "DONE! Now:"
echo "1. Start MySQL from XAMPP Control Panel"
echo "2. Visit: http://localhost:8000/import-database.php"
echo ""