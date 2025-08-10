#!/bin/bash

echo "MySQL Cleanup Script"
echo "==================="
echo ""
echo "This will clean up any stuck MySQL processes"
echo ""

# Kill all MySQL processes
echo "Killing all MySQL processes..."
sudo pkill -9 -f mysql
sudo pkill -9 -f mysqld

echo "Waiting for processes to stop..."
sleep 3

echo ""
echo "Done! Now:"
echo "1. Start MySQL fresh from XAMPP Control Panel"
echo "2. Once it's running, visit: http://localhost:8000/import-database.php"
echo ""
echo "The web-based import should work now with password: root"